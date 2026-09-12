<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\CallableTypeNode;

/**
 * Tests for the template parameters a callable type declares, that is, the
 * names it introduces and the bounds put on them.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TemplateParameterTest extends SyntaxTestCase
{
    public function testCallableDeclaresATemplateParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(T)
              NamedTypeNode
                Name(T)
              Template\TemplateParameterListNode
                Template\TemplateParameterNode
                  Identifier(T)
            AST, $this->parseAndPrint('callable<T>(T): T'));
    }

    public function testEveryLimitIsReadIntoAPlaceOfItsOwn(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(Closure)
              Callable\CallableParameterListNode
              Template\TemplateParameterListNode
                Template\TemplateParameterNode
                  Identifier(T)
                  Template\TemplateBoundEdgeNode
                    Identifier(of)
                    NamedTypeNode
                      Name(Some)
                  Template\TemplateBoundEdgeNode
                    Identifier(super)
                    NamedTypeNode
                      Name(Any)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint('Closure<T of Some super Any = int>()'));
    }

    /**
     * The word an upper bound is written with is kept as it is written, since
     * an "of" and an "as" mean the same and only one of them was typed.
     */
    public function testUpperBoundKeepsTheWordItIsWrittenWith(): void
    {
        $node = $this->parse('Closure<T as Some>()');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);
        self::assertSame('as', $node->templates->items[0]->upper?->operator->value);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, 'upper'|'lower'|'default'}>
     */
    public static function limitDataProvider(): iterable
    {
        yield 'of' => ['callable<T of Some>(): void', 'upper'];
        yield 'as' => ['callable<T as Some>(): void', 'upper'];
        yield 'super' => ['callable<T super Some>(): void', 'lower'];
        yield 'assign' => ['callable<T = Some>(): void', 'default'];
    }

    /**
     * @param non-empty-string $type
     * @param 'upper'|'lower'|'default' $filled
     * @throws \Throwable
     */
    #[DataProvider('limitDataProvider')]
    public function testEveryLimitFillsThePlaceItBelongsTo(string $type, string $filled): void
    {
        $node = $this->parse($type);

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);

        $parameter = $node->templates->items[0];

        self::assertSame('T', $parameter->name->value);

        foreach (['upper', 'lower', 'default'] as $place) {
            if ($place === $filled) {
                self::assertNotNull($parameter->{$place}, "The {$place} must be filled");

                continue;
            }

            self::assertNull($parameter->{$place}, "The {$place} must be left out");
        }
    }

    public function testASingleParameterCarriesEveryLimitItIsWrittenWith(): void
    {
        $node = $this->parse('callable<T of Some super Any = int>(): void');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);

        $parameter = $node->templates->items[0];

        self::assertSame('of', $parameter->upper?->operator->value);
        self::assertSame('super', $parameter->lower?->operator->value);
        self::assertNotNull($parameter->default);
    }

    public function testAParameterWrittenWithNoLimitCarriesNone(): void
    {
        $node = $this->parse('callable<T>(): void');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);
        $parameter = $node->templates->items[0];

        self::assertNull($parameter->upper);
        self::assertNull($parameter->lower);
        self::assertNull($parameter->default);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function invalidLimitDataProvider(): iterable
    {
        yield 'unknown word' => [
            'callable<T whatever Some>(): void',
            'cannot be bounded with "whatever"',
        ];
        yield 'an upper bound of another case' => [
            'callable<T OF Some>(): void',
            'cannot be bounded with "OF"',
        ];
        yield 'a lower bound of another case' => [
            'callable<T SUPER Some>(): void',
            'cannot be bounded with "SUPER"',
        ];
        yield 'two upper bounds' => [
            'callable<T of A of B>(): void',
            'cannot have more than one upper bound',
        ];
        yield 'two lower bounds' => [
            'callable<T super A super B>(): void',
            'cannot have more than one lower bound',
        ];
        yield 'two defaults' => [
            'callable<T = 1 = 2>(): void',
            'cannot have more than one default',
        ];
        yield 'upper bound behind the default' => [
            'callable<T = int of Some>(): void',
            'default must be written last',
        ];
        yield 'lower bound behind the default' => [
            'callable<T = int super Some>(): void',
            'default must be written last',
        ];
    }

    /**
     * The two ends are told apart by the word each is written with, so which
     * of them comes first says nothing.
     */
    public function testTheTwoEndsAreWrittenInEitherOrder(): void
    {
        $node = $this->parse('callable<T super Any of Some>(): void');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);

        $parameter = $node->templates->items[0];

        self::assertSame('of', $parameter->upper?->operator->value);
        self::assertSame('super', $parameter->lower?->operator->value);
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('invalidLimitDataProvider')]
    public function testALimitIsWrittenAtMostOnceAndBoundsSomething(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    public function testSeveralParametersAreRead(): void
    {
        $node = $this->parse('callable<T, U of Some, V>(T, U): V');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);

        $names = [];

        foreach ($node->templates as $parameter) {
            $names[] = $parameter->name->value;
        }

        self::assertSame(['T', 'U', 'V'], $names);
    }

    public function testTrailingCommaIsAllowed(): void
    {
        $node = $this->parse('callable<T, U,>(): void');

        self::assertInstanceOf(CallableTypeNode::class, $node);
        self::assertNotNull($node->templates);
        self::assertCount(2, $node->templates);
    }

    /**
     * A "<...>" that no parenthesis follows is an argument list, and an
     * argument list describes no bounds.
     */
    public function testBoundsBelongToACallableAlone(): void
    {
        $this->expectParsingException();

        $this->parse('Collection<T of Some>');
    }

    public function testATypeUsedWithArgumentsIsNotACallable(): void
    {
        $node = $this->parse('Collection<T>');

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);
    }

    public function testParametersAreRefusedWhenGenericsAreDisabled(): void
    {
        $this->expectParsingException('Template parameters not allowed');

        $this->parse('callable<T>(T): T', ['generics' => false]);
    }

    public function testParametersAreRefusedWhenCallablesAreDisabled(): void
    {
        $this->expectParsingException('Callable types not allowed');

        $this->parse('callable<T>(T): T', ['callables' => false]);
    }
}
