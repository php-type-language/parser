<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * A name with a list of parameters and a return type behind it
 * (grammar/callable.pp3).
 */
final class CallableTypeTest extends TestCase
{
    private function parseCallable(string $source): CallableTypeNode
    {
        $node = $this->parse($source);

        self::assertInstanceOf(CallableTypeNode::class, $node);

        return $node;
    }

    private function parseParameter(string $source): CallableParameterNode
    {
        $parameter = $this->parseCallable($source)->parameters[0];

        self::assertInstanceOf(CallableParameterNode::class, $parameter);

        return $parameter;
    }

    public function testNoParametersAndNoReturnType(): void
    {
        $node = $this->parseCallable('callable()');

        self::assertSame('callable', $node->name->toString());
        self::assertCount(0, $node->parameters);
        self::assertNull($node->type);
        self::assertNull($node->templates);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function nameDataProvider(): iterable
    {
        yield 'callable' => ['callable(int): void', 'callable'];
        yield 'closure' => ['Closure(int): void', 'Closure'];
        yield 'fully qualified closure' => ['\\Closure(int): void', '\\Closure'];
        yield 'qualified name' => ['Foo\\Bar(int): void', 'Foo\\Bar'];
        yield 'virtual name' => ['pure-callable(int): int', 'pure-callable'];
        yield 'any name' => ['foo(T): void', 'foo'];
        yield 'keyword' => ['int(T): void', 'int'];
        yield 'with a gap' => ["Closure /* c */\n(int): void", 'Closure'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $name
     */
    #[DataProvider('nameDataProvider')]
    public function testNameIsTheNameInFrontOfTheParentheses(string $source, string $name): void
    {
        self::assertSame($name, $this->parseCallable($source)->name->toString());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>|null, string|null, bool, bool, bool}>
     */
    public static function parameterDataProvider(): iterable
    {
        // source, type, name, by reference, variadic, has a default
        yield 'type' => ['callable(int)', NamedTypeNode::class, null, false, false, false];
        yield 'type and name' => ['callable(int $a)', NamedTypeNode::class, 'a', false, false, false];
        yield 'name' => ['callable($a)', null, 'a', false, false, false];
        yield 'this as a type' => ['callable($this)', ThisTypeNode::class, null, false, false, false];
        yield 'by reference with a name' => ['callable(int &$a)', NamedTypeNode::class, 'a', true, false, false];
        yield 'by reference with no name' => ['callable(int&)', NamedTypeNode::class, null, true, false, false];
        yield 'by reference with no type' => ['callable(&$a)', null, 'a', true, false, false];
        yield 'variadic' => ['callable(int...)', NamedTypeNode::class, null, false, true, false];
        yield 'variadic with a space' => ['callable(int ...)', NamedTypeNode::class, null, false, true, false];
        yield 'variadic with a name' => ['callable(int ...$a)', NamedTypeNode::class, 'a', false, true, false];
        yield 'variadic by reference' => ['callable(int &...$a)', NamedTypeNode::class, 'a', true, true, false];
        yield 'variadic in front of a type' => ['callable(...int)', NamedTypeNode::class, null, false, true, false];
        yield 'variadic in front of a name' => ['callable(...$a)', null, 'a', false, true, false];
        yield 'variadic in front of a name by reference' => ['callable(&...$a)', null, 'a', true, true, false];
        yield 'optional' => ['callable(int=)', NamedTypeNode::class, null, false, false, true];
        yield 'optional with a name' => ['callable(int $a=)', NamedTypeNode::class, 'a', false, false, true];
        yield 'optional with a name only' => ['callable($a=)', null, 'a', false, false, true];
        yield 'optional by reference' => ['callable(int &$a=)', NamedTypeNode::class, 'a', true, false, true];
        yield 'default' => ['callable(int $a = 42)', NamedTypeNode::class, 'a', false, false, true];
        yield 'default with no name' => ['callable(int = 42)', NamedTypeNode::class, null, false, false, true];
        yield 'intersection type' => ['callable(A&B $x)', IntersectionTypeNode::class, 'x', false, false, false];
        yield 'intersection type by reference' => ['callable(A&B &$x)', IntersectionTypeNode::class, 'x', true, false, false];
        yield 'nullable type' => ['callable(?int $x)', NullableTypeNode::class, 'x', false, false, false];
        yield 'union type' => ['callable(int|string $x)', UnionTypeNode::class, 'x', false, false, false];
        yield 'list type' => ['callable(int[] ...$x)', TypesListTypeNode::class, 'x', false, true, false];
        yield 'callable type' => ['callable(callable(): void $x)', CallableTypeNode::class, 'x', false, false, false];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode>|null $type
     */
    #[DataProvider('parameterDataProvider')]
    public function testParameterKeepsEveryPartOfItself(
        string $source,
        ?string $type,
        ?string $name,
        bool $isOutput,
        bool $isVariadic,
        bool $hasDefault,
    ): void {
        $parameter = $this->parseParameter($source);

        if ($type === null) {
            self::assertNull($parameter->type);
        } else {
            self::assertInstanceOf($type, $parameter->type);
        }

        self::assertSame($name, $parameter->name?->name);
        self::assertSame($isOutput, $parameter->isOutput);
        self::assertSame($isVariadic, $parameter->isVariadic);
        self::assertSame($hasDefault, $parameter->default !== null);
    }

    public function testParametersAreKeptInTheOrderTheyAreWritten(): void
    {
        $node = $this->parseCallable('callable(int $a, string &$b, bool ...$c): void');

        self::assertCount(3, $node->parameters);
        self::assertSame('a', $node->parameters[0]->name?->name);
        self::assertSame('b', $node->parameters[1]->name?->name);
        self::assertSame('c', $node->parameters[2]->name?->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>|null}>
     */
    public static function defaultDataProvider(): iterable
    {
        yield 'none' => ['callable(int $a=)', null];
        yield 'none with no name' => ['callable(int=)', null];
        yield 'integer' => ['callable(int $a = 42)', IntLiteralNode::class];
        yield 'negative integer' => ['callable(int $a = -1)', IntLiteralNode::class];
        yield 'negative integer with a gap' => ['callable(int $a = - 1)', IntLiteralNode::class];
        yield 'hexadecimal' => ['callable(int $a = 0x1F)', IntLiteralNode::class];
        yield 'float' => ['callable(float $a = 1.5)', FloatLiteralNode::class];
        yield 'single quoted string' => ["callable(string \$a = 'x')", StringLiteralNode::class];
        yield 'double quoted string' => ['callable(string $a = "x")', StringLiteralNode::class];
        yield 'true' => ['callable(bool $a = true)', BoolLiteralNode::class];
        yield 'false' => ['callable(bool $a = false)', BoolLiteralNode::class];
        yield 'null' => ['callable(?int $a = null)', NullLiteralNode::class];
        yield 'class constant' => ['callable(int $a = Foo::BAR)', ClassConstTypeNode::class];
        yield 'class constant of a qualified class' => ['callable(int $a = \\Foo\\Bar::BAZ)', ClassConstTypeNode::class];
        yield 'class name' => ['callable(string $a = Foo::class)', ClassConstTypeNode::class];
        yield 'global constant' => ['callable(int $a = PHP_INT_MAX)', NamedTypeNode::class];
        yield 'fully qualified constant' => ['callable(string $a = \\PHP_EOL)', NamedTypeNode::class];
        yield 'with no type' => ['callable($a = 42)', IntLiteralNode::class];
    }

    /**
     * An optional parameter has a default node, the node has no value when
     * nothing is written behind the sign.
     *
     * @param non-empty-string $source
     * @param class-string<TypeNode>|null $value
     */
    #[DataProvider('defaultDataProvider')]
    public function testDefaultValueIsAConstantOrALiteral(string $source, ?string $value): void
    {
        $parameter = $this->parseParameter($source);

        self::assertNotNull($parameter->default);

        if ($value === null) {
            self::assertNull($parameter->default->value);
        } else {
            self::assertInstanceOf($value, $parameter->default->value);
        }
    }

    public function testDefaultValuesAreKept(): void
    {
        $int = $this->parseParameter('callable(int $a = -42)')->default?->value;
        $constant = $this->parseParameter('callable(int $a = Foo::BAR)')->default?->value;
        $global = $this->parseParameter('callable(int $a = \\PHP_EOL)')->default?->value;

        self::assertInstanceOf(IntLiteralNode::class, $int);
        self::assertSame(-42, $int->value);

        self::assertInstanceOf(ClassConstTypeNode::class, $constant);
        self::assertSame('Foo', $constant->class->toString());
        self::assertSame('BAR', $constant->constant->value);

        self::assertInstanceOf(NamedTypeNode::class, $global);
        self::assertSame('\\PHP_EOL', $global->name->toString());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>|null}>
     */
    public static function returnTypeDataProvider(): iterable
    {
        yield 'none' => ['callable(int)', null];
        yield 'name' => ['callable(): void', NamedTypeNode::class];
        yield 'nullable' => ['callable(): ?int', NullableTypeNode::class];
        yield 'list' => ['callable(): int[]', TypesListTypeNode::class];
        yield 'offset' => ['callable(): T[K]', TypeOffsetAccessTypeNode::class];
        yield 'generic' => ['callable(): array<int, string>', NamedTypeNode::class];
        yield 'shape' => ['callable(): array{a: int}', NamedTypeNode::class];
        yield 'this' => ['callable(): $this', ThisTypeNode::class];
        yield 'literal' => ['callable(): 42', IntLiteralNode::class];
        yield 'class constant' => ['callable(): Foo::BAR', ClassConstTypeNode::class];
        yield 'group of a union' => ['callable(): (int|string)', UnionTypeNode::class];
        yield 'callable' => ['callable(): callable(): void', CallableTypeNode::class];
        yield 'gap around the colon' => ["callable(int) /* c */ :\n void", NamedTypeNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode>|null $type
     */
    #[DataProvider('returnTypeDataProvider')]
    public function testReturnType(string $source, ?string $type): void
    {
        $node = $this->parseCallable($source);

        if ($type === null) {
            self::assertNull($node->type);
        } else {
            self::assertInstanceOf($type, $node->type);
        }
    }

    public function testNestedReturnTypeIsTheInnerCallable(): void
    {
        $node = $this->parseCallable('callable(): callable(): void');

        self::assertInstanceOf(CallableTypeNode::class, $node->type);
        self::assertCount(0, $node->type->parameters);
        self::assertInstanceOf(NamedTypeNode::class, $node->type->type);
    }

    /**
     * The return type takes one type with the suffixes only, the operators
     * stay outside: `callable(): int|string` is `(callable(): int)|string`.
     */
    public function testUnionBehindTheReturnTypeIsOutsideOfTheCallable(): void
    {
        $node = $this->parse('callable(): int|string');

        self::assertInstanceOf(UnionTypeNode::class, $node);
        self::assertInstanceOf(CallableTypeNode::class, $node->types[0]);
        self::assertInstanceOf(NamedTypeNode::class, $node->types[0]->type);
        self::assertInstanceOf(NamedTypeNode::class, $node->types[1]);
    }

    public function testIntersectionBehindTheReturnTypeIsOutsideOfTheCallable(): void
    {
        $node = $this->parse('callable(): A&B');

        self::assertInstanceOf(IntersectionTypeNode::class, $node);
        self::assertInstanceOf(CallableTypeNode::class, $node->types[0]);
        self::assertInstanceOf(NamedTypeNode::class, $node->types[1]);
    }

    public function testSuffixesBehindTheReturnTypeAreItsOwn(): void
    {
        $node = $this->parseCallable('callable(): int[][K]');

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node->type);
        self::assertInstanceOf(TypesListTypeNode::class, $node->type->type);
    }

    public function testNullableMarkBehindTheColonIsOfTheReturnType(): void
    {
        $node = $this->parseCallable('callable(): ?int');

        self::assertInstanceOf(NullableTypeNode::class, $node->type);
    }

    public function testNullableMarkInFrontIsOfTheWholeCallable(): void
    {
        $node = $this->parse('?callable(): int');

        self::assertInstanceOf(NullableTypeNode::class, $node);
        self::assertInstanceOf(CallableTypeNode::class, $node->type);
        self::assertInstanceOf(NamedTypeNode::class, $node->type->type);
    }

    public function testListOfCallablesNeedsTheGroup(): void
    {
        $node = $this->parse('(callable(): int)[]');

        self::assertInstanceOf(TypesListTypeNode::class, $node);
        self::assertInstanceOf(CallableTypeNode::class, $node->type);
    }

    public function testSuffixBehindTheReturnTypeIsOfTheReturnType(): void
    {
        $node = $this->parse('callable(): int[]');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertInstanceOf(TypesListTypeNode::class, $node->type);
    }

    /**
     * A callable begins at its name, a parameter at its first token.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, list<int<0, max>>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'simple' => ['callable(int $a): void', 0, [9]];
        yield 'spaces before' => ['  callable(int $a): void', 2, [11]];
        yield 'gap before the parenthesis' => ['callable (int $a): void', 0, [10]];
        yield 'two parameters' => ['callable(int $a, string $b): void', 0, [9, 17]];
        yield 'by reference first' => ['callable(&$a, int ...$b)', 0, [9, 14]];
        yield 'variadic first' => ['callable(...int, $a)', 0, [9, 17]];
        yield 'default' => ['callable(int $a = 42, $b)', 0, [9, 22]];
        yield 'variadic with no type' => ['callable(...$a, &...$b)', 0, [9, 16]];
        yield 'by reference with no type' => ['callable(&$a, $b = 1)', 0, [9, 14]];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     * @param list<int<0, max>> $parameters
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $offset, array $parameters): void
    {
        $node = $this->parseCallable($source);
        $actual = [];

        foreach ($node->parameters as $parameter) {
            $actual[] = $parameter->offset;
        }

        self::assertSame($offset, $node->offset);
        self::assertSame($offset, $node->name->offset);
        self::assertSame($parameters, $actual);
    }

    public function testOffsetsOfTheDefaultValueAndTheName(): void
    {
        $parameter = $this->parseParameter('callable(int $a = 42)');

        self::assertSame(9, $parameter->offset);
        self::assertSame(9, $parameter->type?->offset);
        self::assertSame(13, $parameter->name?->offset);
        self::assertSame(16, $parameter->default?->offset);
        self::assertSame(18, $parameter->default->value?->offset);
    }

    public function testCallableAsAnArgument(): void
    {
        $node = $this->parse('Foo<callable(int): void>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments[0];

        self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
        self::assertNull($argument->variance);
        self::assertInstanceOf(CallableTypeNode::class, $argument->value);
    }

    /**
     * A word that is no variance and has a parenthesis behind it names a callable.
     */
    public function testWordBeforeTheGroupInTheArgumentIsTheNameOfACallable(): void
    {
        $node = $this->parse('Foo<in (A|B)>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments[0];

        self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
        self::assertNull($argument->variance);
        self::assertInstanceOf(CallableTypeNode::class, $argument->value);
        self::assertSame('in', $argument->value->name->toString());
        self::assertInstanceOf(UnionTypeNode::class, $argument->value->parameters[0]->type);
    }

    /**
     * The words covariant, contravariant and invariant are reserved for the
     * variance, a group behind one is the type of the argument.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function varianceBeforeTheGroupDataProvider(): iterable
    {
        yield 'covariant' => ['Foo<covariant (A|B)>', 'covariant'];
        yield 'covariant with no space' => ['Foo<covariant(A|B)>', 'covariant'];
        yield 'contravariant' => ['Foo<contravariant (A|B)>', 'contravariant'];
        yield 'invariant' => ['Foo<invariant (A|B)>', 'invariant'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $variance
     */
    #[DataProvider('varianceBeforeTheGroupDataProvider')]
    public function testVarianceBeforeTheGroupIsAVarianceOfAGroup(string $source, string $variance): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments[0];

        self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
        self::assertSame($variance, $argument->variance?->kind->value);
        self::assertInstanceOf(UnionTypeNode::class, $argument->value);
    }

    public function testVarianceInFrontOfAUnionWithNoParenthesesIsAVariance(): void
    {
        $node = $this->parse('Foo<covariant A|B>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments[0];

        self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
        self::assertSame(VarianceKind::Covariant, $argument->variance?->kind);
        self::assertInstanceOf(UnionTypeNode::class, $argument->value);
    }

    public function testVarianceInFrontOfACallableIsAVariance(): void
    {
        $node = $this->parse('Foo<covariant callable(int): void>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments[0];

        self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
        self::assertSame(VarianceKind::Covariant, $argument->variance?->kind);
        self::assertInstanceOf(CallableTypeNode::class, $argument->value);
    }

    public function testDisabledCallablesAreRejected(): void
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Callables feature is not allowed');

        $this->parse('callable(int): void', ['callables' => false]);
    }

    public function testDisabledCallablesAreRejectedWithNoParameters(): void
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Callables feature is not allowed');

        $this->parse('callable()', ['callables' => false]);
    }

    public function testDisabledDefaultsAreRejected(): void
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Defaults feature is not allowed');

        $this->parse('callable(int $a = 42): void', ['defaults' => false]);
    }

    public function testDisabledDefaultsAreRejectedWithNoValue(): void
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Defaults feature is not allowed');

        $this->parse('callable(int=): void', ['defaults' => false]);
    }

    public function testDisabledDefaultsDoNotTouchTheRest(): void
    {
        $node = $this->parse('callable(int $a, string ...$b): array{a: int}', ['defaults' => false]);

        self::assertInstanceOf(CallableTypeNode::class, $node);
    }

    public function testDisabledCallablesDoNotTouchTheRest(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('Foo<int>', ['callables' => false]));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('array{a: int}', ['callables' => false]));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('callable', ['callables' => false]));
    }
}
