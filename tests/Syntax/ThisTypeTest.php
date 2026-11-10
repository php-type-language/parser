<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The type `$this` (grammar.pp3).
 */
final class ThisTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validThisDataProvider(): iterable
    {
        yield 'this' => ['$this'];
        yield 'spaces around' => ['  $this  '];
        yield 'line breaks around' => ["\n\$this\r\n"];
        yield 'block comment before' => ['/* c */ $this'];
        yield 'line comment behind' => ['$this // c'];
        yield 'hash comment behind' => ["\$this # c\n"];
    }

    #[DataProvider('validThisDataProvider')]
    public function testThisIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidThisDataProvider(): iterable
    {
        yield 'upper case' => ['$THIS'];
        yield 'capitalized' => ['$This'];
        yield 'with a suffix' => ['$thisFoo'];
        yield 'with an underscore behind' => ['$this_'];
        yield 'with a digit behind' => ['$this1'];
        yield 'with a hyphen behind' => ['$this-x'];
        yield 'other variable' => ['$that'];
        yield 'lone dollar' => ['$'];
        yield 'class constant of this' => ['$this::Foo'];
        yield 'namespace of this' => ['$this\\Foo'];
        yield 'fully qualified this' => ['\\$this'];
        yield 'glued to a name' => ['Foo$this'];
        yield 'two of them' => ['$this $this'];
    }

    #[DataProvider('invalidThisDataProvider')]
    public function testThisIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
