<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use Phplrt\Source\StringSource;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;
use TypeLang\Type\TypeNode;

/**
 * A template parameter: a name, a variance, the bounds and a default
 * (grammar/templates/parameters.pp3).
 */
final class TemplateParameterNodeTest extends TestCase
{
    private function readParameter(string $source): TemplateParameterNode
    {
        $node = (new Executor(new TypeParserFeatures()))
            ->for(Executor::TemplateParameter)
            ->parse(new StringSource($source));

        self::assertInstanceOf(TemplateParameterNode::class, $node);

        return $node;
    }

    private function parseCallable(string $source): CallableTypeNode
    {
        $node = $this->parse($source);

        self::assertInstanceOf(CallableTypeNode::class, $node);

        return $node;
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, VarianceKind|null, string|null, string|null, bool}>
     */
    public static function parameterDataProvider(): iterable
    {
        // source, name, variance, the word of the upper bound, the word of the lower bound, has a default
        yield 'name' => ['T', 'T', null, null, null, false];
        yield 'upper by of' => ['T of U', 'T', null, 'of', null, false];
        yield 'upper by as' => ['T as U', 'T', null, 'as', null, false];
        yield 'lower' => ['T super U', 'T', null, null, 'super', false];
        yield 'both' => ['T of A super B', 'T', null, 'of', 'super', false];
        yield 'default' => ['T = X', 'T', null, null, null, true];
        yield 'bound and default' => ['T of U = X', 'T', null, 'of', null, true];
        yield 'covariant' => ['covariant T', 'T', VarianceKind::Covariant, null, null, false];
        yield 'contravariant' => ['contravariant T', 'T', VarianceKind::Contravariant, null, null, false];
        yield 'invariant' => ['invariant T', 'T', VarianceKind::Invariant, null, null, false];
        yield 'everything' => ['covariant T of U super V = X', 'T', VarianceKind::Covariant, 'of', 'super', true];
        yield 'gaps' => ["covariant\nT /* c */ of\tU  super V=X", 'T', VarianceKind::Covariant, 'of', 'super', true];
        yield 'keyword is as a name' => ['is', 'is', null, null, null, false];
        yield 'keyword of as a name' => ['of', 'of', null, null, null, false];
        yield 'keyword of as a name with a bound' => ['of of int', 'of', null, 'of', null, false];
        yield 'keyword as a name and as a bound' => ['as as int', 'as', null, 'as', null, false];
        yield 'keyword super as a name and a bound' => ['super super int', 'super', null, null, 'super', false];
        yield 'variance word as a name' => ['covariant', 'covariant', null, null, null, false];
        yield 'variance and the variance word as a name' => ['covariant covariant', 'covariant', VarianceKind::Covariant, null, null, false];
        yield 'builtin as a name' => ['int', 'int', null, null, null, false];
        yield 'null as a name' => ['null', 'null', null, null, null, false];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $name
     */
    #[DataProvider('parameterDataProvider')]
    public function testParameterKeepsEveryPartOfItself(
        string $source,
        string $name,
        ?VarianceKind $variance,
        ?string $upper,
        ?string $lower,
        bool $hasDefault,
    ): void {
        $parameter = $this->readParameter($source);

        self::assertSame($name, $parameter->name->value);
        self::assertSame($variance, $parameter->variance?->kind);
        self::assertSame($upper !== null, $parameter->upper !== null);
        self::assertSame($lower !== null, $parameter->lower !== null);
        self::assertSame($hasDefault, $parameter->default !== null);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string|null, non-empty-string|null}>
     */
    public static function boundDataProvider(): iterable
    {
        // source, word of the upper bound, word of the lower bound
        yield 'of' => ['T of U', 'of', null];
        yield 'as' => ['T as U', 'as', null];
        yield 'super' => ['T super U', null, 'super'];
        yield 'of and super' => ['T of A super B', 'of', 'super'];
        yield 'as and super' => ['T as A super B', 'as', 'super'];
        yield 'none' => ['T', null, null];
        yield 'keyword of as a name and as a bound' => ['of of int', 'of', null];
        yield 'keyword super as a name and as a bound' => ['super super int', null, 'super'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string|null $upper
     * @param non-empty-string|null $lower
     */
    #[DataProvider('boundDataProvider')]
    public function testBoundKeepsTheWordItIsWrittenWith(string $source, ?string $upper, ?string $lower): void
    {
        $parameter = $this->readParameter($source);

        self::assertSame($upper, $parameter->upper?->operator->value);
        self::assertSame($lower, $parameter->lower?->operator->value);
    }

    public function testBoundsAreTypes(): void
    {
        $parameter = $this->readParameter('T of A|B super C = D');

        self::assertInstanceOf(TemplateBoundNode::class, $parameter->upper);
        self::assertInstanceOf(UnionTypeNode::class, $parameter->upper->type);
        self::assertInstanceOf(TemplateBoundNode::class, $parameter->lower);
        self::assertInstanceOf(NamedTypeNode::class, $parameter->lower->type);
        self::assertSame('C', $parameter->lower->type->name->toString());
        self::assertInstanceOf(NamedTypeNode::class, $parameter->default);
        self::assertSame('D', $parameter->default->name->toString());
    }

    public function testDefaultMayBeAnyType(): void
    {
        $parameter = $this->readParameter('T of int = 42');

        self::assertInstanceOf(IntLiteralNode::class, $parameter->default);
        self::assertSame(42, $parameter->default->value);
    }

    public function testNestedGenericsInABound(): void
    {
        $parameter = $this->readParameter('T of Foo<Bar<int>>');

        self::assertInstanceOf(NamedTypeNode::class, $parameter->upper?->type);
        self::assertNotNull($parameter->upper->type->arguments);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function invalidParameterDataProvider(): iterable
    {
        yield 'two upper bounds' => ['T of U of V'];
        yield 'two upper bounds of both kinds' => ['T of U as V'];
        yield 'two lower bounds' => ['T super U super V'];
        yield 'lower bound before an upper one' => ['T super U of V'];
        yield 'default before a bound' => ['T = U of V'];
        yield 'upper case bound' => ['T OF U'];
        yield 'extends' => ['T extends U'];
        yield 'two variances' => ['covariant contravariant T'];
        yield 'in as a variance' => ['in T'];
        yield 'a list' => ['T, U'];
        // A variance word in front of a word is a variance, so the name is "of" and "int" is left over
        yield 'variance word as a name with a bound' => ['invariant of int'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('invalidParameterDataProvider')]
    public function testInvalidParameterIsRejected(string $source): void
    {
        $this->expectException(\Throwable::class);

        $this->readParameter($source);
    }

    public function testParametersOfACallable(): void
    {
        $node = $this->parseCallable('callable<T, U of int>(T $a): U');

        self::assertNotNull($node->templates);
        self::assertCount(2, $node->templates);
        self::assertSame('T', $node->templates[0]->name->value);
        self::assertSame('U', $node->templates[1]->name->value);
        self::assertSame('of', $node->templates[1]->upper?->operator->value);
        self::assertCount(1, $node->parameters);
        self::assertInstanceOf(NamedTypeNode::class, $node->type);
    }

    public function testParametersOfACallableWithNoParameters(): void
    {
        $node = $this->parseCallable('callable<T>(): T');

        self::assertNotNull($node->templates);
        self::assertCount(1, $node->templates);
        self::assertCount(0, $node->parameters);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function nameDataProvider(): iterable
    {
        yield 'callable' => ['callable<T>(T): T', 'callable'];
        yield 'closure' => ['Closure<T>(T): T', 'Closure'];
        yield 'fully qualified closure' => ['\\Closure<T>(T): T', '\\Closure'];
        yield 'pure callable' => ['pure-callable<T>(T): T', 'pure-callable'];
        yield 'any name' => ['Foo<T>(T): T', 'Foo'];
        yield 'with a gap' => ["Foo <T> /* c */ (T): T", 'Foo'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $name
     */
    #[DataProvider('nameDataProvider')]
    public function testNameOfACallableWithParameters(string $source, string $name): void
    {
        $node = $this->parseCallable($source);

        self::assertSame($name, $node->name->toString());
        self::assertNotNull($node->templates);
    }

    public function testCallableWithNoTemplatesHasNone(): void
    {
        self::assertNull($this->parseCallable('callable(int): void')->templates);
    }

    public function testListWithNoParenthesesAreArguments(): void
    {
        $node = $this->parse('callable<T>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
    }

    public function testNestedCallableInABound(): void
    {
        $node = $this->parseCallable('callable<T of callable<U>(U): U>(T): T');

        self::assertNotNull($node->templates);

        $bound = $node->templates[0]->upper?->type;

        self::assertInstanceOf(CallableTypeNode::class, $bound);
        self::assertNotNull($bound->templates);
        self::assertSame('U', $bound->templates[0]->name->value);
    }

    public function testNestedArgumentsAreNotParameters(): void
    {
        $node = $this->parse('Foo<Bar<Baz<int>>>');

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        // source, offset of the parameter, offset of its name
        yield 'name' => ['T', 0, 0];
        yield 'spaces before' => ['  T', 2, 2];
        yield 'variance' => ['covariant T', 0, 10];
        yield 'variance with a gap' => ["covariant  \nT", 0, 12];
        yield 'bound' => ['T of U', 0, 0];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     * @param int<0, max> $name
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $offset, int $name): void
    {
        $parameter = $this->readParameter($source);

        self::assertSame($offset, $parameter->offset);
        self::assertSame($name, $parameter->name->offset);
    }

    public function testOffsetsOfTheVarianceAndTheBounds(): void
    {
        $parameter = $this->readParameter('covariant T of A super B');

        self::assertSame(0, $parameter->variance?->offset);
        self::assertSame(12, $parameter->upper?->offset);
        self::assertSame(15, $parameter->upper->type->offset);
        self::assertSame(17, $parameter->lower?->offset);
        self::assertSame(23, $parameter->lower->type->offset);
    }

    public function testDisabledTemplatesAreRejected(): void
    {
        $this->expectException(FeatureNotEnabledException::class);
        $this->expectExceptionMessage('Templates feature is not allowed');

        $this->parse('callable<T>(T): T', ['templates' => false]);
    }

    public function testDisabledTemplatesDoNotTouchTheRest(): void
    {
        self::assertInstanceOf(CallableTypeNode::class, $this->parse('callable(int): void', ['templates' => false]));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('Foo<int>', ['templates' => false]));
    }

    public function testTypeIsNotAParameter(): void
    {
        self::assertInstanceOf(TypeNode::class, $this->parse('Foo<T>'));
    }
}
