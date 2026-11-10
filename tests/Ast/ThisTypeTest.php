<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;

/**
 * The type `$this` is not a name.
 */
final class ThisTypeTest extends TestCase
{
    public function testThisIsAThisTypeNode(): void
    {
        self::assertInstanceOf(ThisTypeNode::class, $this->parse('$this'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['$this', 0];
        yield 'spaces' => ['  $this', 2];
        yield 'line break' => ["\n\n\$this", 2];
        yield 'block comment' => ['/* c */ $this', 8];
        yield 'line comment' => ["// c\n\$this", 5];
        yield 'multibyte before' => ['/* Фу */ $this', 11];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetPointsToTheDollar(string $source, int $offset): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(ThisTypeNode::class, $type);
        self::assertSame($offset, $type->offset);
    }

    public function testTriviaAroundIsIgnored(): void
    {
        self::assertInstanceOf(ThisTypeNode::class, $this->parse("/* a */ \$this // b\n"));
    }

    public function testThisWordWithNoDollarIsAName(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('this'));
    }
}
