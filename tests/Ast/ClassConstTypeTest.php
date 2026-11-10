<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\NamedTypeNode;

/**
 * A constant of a class and a mask of constants.
 */
final class ClassConstTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function constantDataProvider(): iterable
    {
        yield 'constant' => ['Foo::BAR', 'Foo', 'BAR'];
        yield 'constant in lower case' => ['Foo::bar', 'Foo', 'bar'];
        yield 'class of a namespace' => ['Foo\\Bar::BAZ', 'Foo\\Bar', 'BAZ'];
        yield 'fully qualified class' => ['\\Foo\\Bar::BAZ', '\\Foo\\Bar', 'BAZ'];
        yield 'special class' => ['static::BAR', 'static', 'BAR'];
        yield 'class name' => ['Foo::class', 'Foo', 'class'];
        yield 'constant named like a literal' => ['Foo::NULL', 'Foo', 'NULL'];
        yield 'constant named like a keyword' => ['Foo::is', 'Foo', 'is'];
        yield 'spaces around the colons' => ['Foo :: BAR', 'Foo', 'BAR'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $class
     * @param non-empty-string $constant
     */
    #[DataProvider('constantDataProvider')]
    public function testConstantIsBuilt(string $source, string $class, string $constant): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(ClassConstTypeNode::class, $type);
        self::assertSame($class, $type->class->toString());
        self::assertSame($constant, $type->constant->value);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, list<class-string>}>
     */
    public static function maskDataProvider(): iterable
    {
        $word = Identifier::class;
        $star = WildcardMaskNode::class;

        yield 'star' => ['Foo::*', '*', [$star]];
        yield 'suffix' => ['Foo::BAR_*', 'BAR_*', [$word, $star]];
        yield 'prefix' => ['Foo::*_BAZ', '*_BAZ', [$star, $word]];
        yield 'inside' => ['Foo::B*R', 'B*R', [$word, $star, $word]];
        yield 'two stars' => ['Foo::A*B*', 'A*B*', [$word, $star, $word, $star]];
        yield 'both ends' => ['Foo::*BAR*', '*BAR*', [$star, $word, $star]];
        yield 'three words' => ['Foo::A*B*C', 'A*B*C', [$word, $star, $word, $star, $word]];
        yield 'keyword' => ['Foo::is*', 'is*', [$word, $star]];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $mask
     * @param list<class-string> $classes
     */
    #[DataProvider('maskDataProvider')]
    public function testMaskIsBuilt(string $source, string $mask, array $classes): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(ClassConstMaskTypeNode::class, $type);
        self::assertSame('Foo', $type->class->toString());
        self::assertSame($mask, $type->mask->toString());
        self::assertSame($classes, \array_map(static fn(object $item): string => $item::class, $type->mask->items));
    }

    public function testSegmentsOfAMaskAreTheWordsAlone(): void
    {
        $type = $this->parse('Foo::A*B*C');

        self::assertInstanceOf(ClassConstMaskTypeNode::class, $type);
        self::assertSame(['A', 'B', 'C'], $type->mask->getSegmentsAsStrings());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['Foo::BAR', 0, 5];
        yield 'spaces before' => ['  Foo::BAR', 2, 7];
        yield 'block comment before' => ['/* c */ Foo::BAR', 8, 13];
        yield 'spaces around the colons' => ['Foo :: BAR', 0, 7];
        yield 'fully qualified class' => ['\\Foo::BAR', 0, 6];
        yield 'multibyte class' => ['Фу::BAR', 0, 6];
    }

    /**
     * The offset of the type is the one of its class, of the constant is the one of its word.
     *
     * @param non-empty-string $source
     * @param int<0, max> $type
     * @param int<0, max> $constant
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $type, int $constant): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(ClassConstTypeNode::class, $node);
        self::assertSame($type, $node->offset);
        self::assertSame($type, $node->class->offset);
        self::assertSame($constant, $node->constant->offset);
    }

    public function testOffsetOfAMaskIsTheOneOfItsFirstItem(): void
    {
        $node = $this->parse('  Foo::BAR_*');

        self::assertInstanceOf(ClassConstMaskTypeNode::class, $node);
        self::assertSame(2, $node->offset);
        self::assertSame(7, $node->mask->offset);
        self::assertSame(7, $node->mask->items[0]->offset);
        self::assertSame(11, $node->mask->items[1]->offset);
    }

    public function testTriviaAroundIsIgnored(): void
    {
        $node = $this->parse("/* a */ Foo::BAR // b\n");

        self::assertInstanceOf(ClassConstTypeNode::class, $node);
        self::assertSame('BAR', $node->constant->value);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function invalidDataProvider(): iterable
    {
        yield 'no constant' => ['Foo::'];
        yield 'no class' => ['::BAR'];
        yield 'one colon' => ['Foo:BAR'];
        yield 'constant of a constant' => ['Foo::BAR::BAZ'];
        yield 'two stars in a row' => ['Foo::**'];
        yield 'two words' => ['Foo::A B'];
        yield 'number as a constant' => ['Foo::1'];
        yield 'namespaced constant' => ['Foo::Bar\\Baz'];
        yield 'this as a class' => ['$this::BAR'];
        yield 'literal as a class' => ['true::BAR'];
        yield 'lone star' => ['*'];
        yield 'bar behind a constant' => ['Foo::BAR|'];
        yield 'unclosed comment' => ['Foo::BAR /*'];
    }

    /**
     * A source that is no constant is rejected by the parser, not by a crash of a reducer.
     *
     * @param non-empty-string $source
     */
    #[DataProvider('invalidDataProvider')]
    public function testInvalidConstantIsNotBuilt(string $source): void
    {
        try {
            $this->parse($source);
        } catch (\Throwable $e) {
            self::assertNotInstanceOf(\Error::class, $e);

            return;
        }

        self::fail(\sprintf('The source %s must not be parsed', \json_encode($source, \JSON_THROW_ON_ERROR)));
    }

    public function testNameWithNoColonsStaysAName(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('Foo\\BAR'));
    }
}
