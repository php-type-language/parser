<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The angle brackets `<...>` attach template arguments to a named type.
 */
final class TemplateArgumentsTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, list<class-string<TypeNode>>}>
     */
    public static function argumentsDataProvider(): iterable
    {
        yield 'single name' => ['Foo<int>', [NamedTypeNode::class]];
        yield 'two names' => ['Foo<int, string>', [NamedTypeNode::class, NamedTypeNode::class]];
        yield 'three names' => ['Foo<A, B, C>', [NamedTypeNode::class, NamedTypeNode::class, NamedTypeNode::class]];
        yield 'qualified name' => ['Foo<\\Bar\\Baz>', [NamedTypeNode::class]];
        yield 'literals' => ['Foo<1, 2.5, true, false, null, "x">', [
            IntLiteralNode::class,
            FloatLiteralNode::class,
            BoolLiteralNode::class,
            BoolLiteralNode::class,
            NullLiteralNode::class,
            StringLiteralNode::class,
        ]];
        yield 'this' => ['Foo<$this>', [ThisTypeNode::class]];
        yield 'class constant' => ['Foo<Bar::BAZ>', [ConstTypeNode::class]];
        yield 'class mask' => ['Foo<Bar::*>', [ConstMaskTypeNode::class]];
        yield 'global mask' => ['Foo<PHP_*>', [ConstMaskTypeNode::class]];
        yield 'nullable' => ['Foo<?int>', [NullableTypeNode::class]];
        yield 'list' => ['Foo<int[]>', [TypesListTypeNode::class]];
        yield 'offset access' => ['Foo<T[K]>', [TypeOffsetAccessTypeNode::class]];
        yield 'union' => ['Foo<int|string>', [UnionTypeNode::class]];
        yield 'intersection' => ['Foo<A&B>', [IntersectionTypeNode::class]];
        yield 'group' => ['Foo<(A|B)>', [UnionTypeNode::class]];
    }

    /**
     * @param non-empty-string $source
     * @param list<class-string<TypeNode>> $expectedArgumentTypes
     */
    #[DataProvider('argumentsDataProvider')]
    public function testArgumentsAreParsed(string $source, array $expectedArgumentTypes): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertInstanceOf(TemplateListNode::class, $node->arguments);
        self::assertCount(\count($expectedArgumentTypes), $node->arguments);

        foreach ($expectedArgumentTypes as $index => $expectedClass) {
            $argument = $node->arguments[$index];
            self::assertInstanceOf(TypedTemplateArgumentNode::class, $argument);
            self::assertInstanceOf($expectedClass, $argument->value);
        }
    }

    public function testWildcardArgument(): void
    {
        $node = $this->parse('Foo<*, int, *>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
        self::assertCount(3, $node->arguments);

        self::assertInstanceOf(WildcardTemplateArgumentNode::class, $node->arguments[0]);
        self::assertInstanceOf(TypedTemplateArgumentNode::class, $node->arguments[1]);
        self::assertInstanceOf(WildcardTemplateArgumentNode::class, $node->arguments[2]);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function varianceDataProvider(): iterable
    {
        yield 'covariant' => ['Foo<covariant T>', 'covariant', 'T'];
        yield 'contravariant' => ['Foo<contravariant T>', 'contravariant', 'T'];
        yield 'invariant' => ['Foo<invariant T>', 'invariant', 'T'];
    }

    /**
     * Only covariant, contravariant and invariant are the variances.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function notVarianceDataProvider(): iterable
    {
        yield 'in' => ['Foo<in T>'];
        yield 'out' => ['Foo<out T>'];
        yield 'any word' => ['Foo<A B>'];
        yield 'keyword is' => ['Foo<is T>'];
        yield 'keyword of' => ['Foo<of T>'];
        yield 'keyword as' => ['Foo<as T>'];
        yield 'keyword super' => ['Foo<super T>'];
        yield 'variance in upper case' => ['Foo<COVARIANT T>'];
        yield 'variance in camel case' => ['Foo<Covariant T>'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('notVarianceDataProvider')]
    public function testWordIsNotAVariance(string $source): void
    {
        $this->expectException(\Throwable::class);

        $this->parse($source);
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $expectedVariance
     * @param non-empty-string $expectedTypeName
     */
    #[DataProvider('varianceDataProvider')]
    public function testVarianceIsParsed(string $source, string $expectedVariance, string $expectedTypeName): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
        self::assertCount(1, $node->arguments);

        $arg = $node->arguments[0];
        self::assertInstanceOf(TypedTemplateArgumentNode::class, $arg);
        self::assertNotNull($arg->variance);
        self::assertInstanceOf(VarianceNode::class, $arg->variance);
        self::assertSame($expectedVariance, $arg->variance->kind->value);
        self::assertInstanceOf(NamedTypeNode::class, $arg->value);
        self::assertSame($expectedTypeName, $arg->value->name->toString());
    }

    public function testTrailingCommaIsAllowed(): void
    {
        $node = $this->parse('Foo<int, string,>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
        self::assertCount(2, $node->arguments);
    }

    public function testDeepNestingOfTemplateArguments(): void
    {
        $source = 'Foo' . \str_repeat('<Foo', 20) . '<int>' . \str_repeat('>', 20);
        $node = $this->parse($source);

        $current = $node;
        for ($i = 0; $i < 21; ++$i) {
            self::assertInstanceOf(NamedTypeNode::class, $current);
            self::assertNotNull($current->arguments);
            self::assertCount(1, $current->arguments);

            $arg = $current->arguments[0];
            self::assertInstanceOf(TypedTemplateArgumentNode::class, $arg);
            $current = $arg->value;
        }

        self::assertInstanceOf(NamedTypeNode::class, $current);
        self::assertSame('int', $current->name->toString());
        self::assertNull($current->arguments);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>, list<int<0, max>>}>
     */
    public static function offsetDataProvider(): iterable
    {
        // source, typeOffset, listOffset (where the first argument begins), [argOffsets...]
        yield 'simple' => ['Foo<int>', 0, 4, [4]];
        yield 'spaces before' => ['  Foo<int>', 2, 6, [6]];
        yield 'spaces around brackets' => ['Foo < int >', 0, 6, [6]];
        yield 'multiple' => ['Foo<int, string>', 0, 4, [4, 9]];
        yield 'variance' => ['Foo<covariant T>', 0, 4, [4]];
        yield 'wildcard' => ['Foo<*>', 0, 4, [4]];
        yield 'wildcard with multiple' => ['Foo<int, *>', 0, 4, [4, 9]];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $typeOffset
     * @param int<0, max> $listOffset
     * @param list<int<0, max>> $argOffsets
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $typeOffset, int $listOffset, array $argOffsets): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertSame($typeOffset, $node->offset);
        self::assertSame($typeOffset, $node->name->offset);
        self::assertNotNull($node->arguments);
        self::assertSame($listOffset, $node->arguments->offset);

        $actualArgOffsets = [];
        foreach ($node->arguments as $arg) {
            $actualArgOffsets[] = $arg->offset;
        }

        self::assertSame($argOffsets, $actualArgOffsets);
    }

    public function testVarianceOffsets(): void
    {
        $node = $this->parse('Foo<covariant string>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $arg = $node->arguments[0];
        self::assertInstanceOf(TypedTemplateArgumentNode::class, $arg);
        self::assertSame(4, $arg->offset);
        self::assertNotNull($arg->variance);
        self::assertSame(4, $arg->variance->offset);
        self::assertSame(14, $arg->value->offset);
    }

    public function testTriviaInsideAndAroundIsIgnored(): void
    {
        $node = $this->parse("/* a */ Foo /* b */ < /* c */ int /* d */ , /* e */ string /* f */ , /* g */ > // h\n");

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
        self::assertCount(2, $node->arguments);
    }

    public function testDisabledGenericsFeatureIsRejected(): void
    {
        $parser = new TypeParser(new TypeParserFeatures(generics: false));

        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Generics feature is not allowed');

        $parser->parse('Foo<int>');
    }

    public function testDisabledWildcardsFeatureIsRejected(): void
    {
        $parser = new TypeParser(new TypeParserFeatures(wildcards: false));

        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Wildcards feature is not allowed');

        $parser->parse('Foo<*>');
    }

    public function testDisabledHintsFeatureIsRejected(): void
    {
        $parser = new TypeParser(new TypeParserFeatures(hints: false));

        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Hints feature is not allowed');

        $parser->parse('Foo<covariant int>');
    }
}
