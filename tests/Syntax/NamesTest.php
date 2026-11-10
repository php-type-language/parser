<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Names of types (grammar/names.pp3).
 */
final class NamesTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validNameDataProvider(): iterable
    {
        yield 'simple' => ['Foo'];
        yield 'fully qualified' => ['\\Foo'];
        yield 'qualified' => ['Foo\\Bar'];
        yield 'fully qualified and qualified' => ['\\Foo\\Bar\\Baz'];
        yield 'leading underscore' => ['_foo'];
        yield 'with digits' => ['Foo1'];
        yield 'with underscore' => ['Foo_Bar'];

        yield 'hyphen in the middle' => ['non-empty-string'];

        yield 'cyrillic' => ['Фу'];
        yield 'cyrillic namespace' => ['Фу\\Бар'];
        yield 'emoji' => ['😀'];
        yield 'no-break space is a part of a name' => ["Foo\u{A0}Bar"];

        yield 'builtin' => ['int'];
        yield 'special' => ['static'];
        yield 'parent' => ['parent'];
        yield 'word of a syntax: class' => ['class'];
        yield 'word of a syntax: list' => ['list'];
        yield 'word of a syntax: array' => ['array'];
        yield 'word of a syntax: callable' => ['callable'];

        yield 'keyword is' => ['is'];
        yield 'keyword not' => ['not'];
        yield 'keyword of' => ['of'];
        yield 'keyword as' => ['as'];
        yield 'keyword super' => ['super'];
        yield 'keyword covariant' => ['covariant'];
        yield 'keyword contravariant' => ['contravariant'];
        yield 'keyword invariant' => ['invariant'];
        yield 'keyword in a namespace' => ['Foo\\is'];
        yield 'variance keyword in a namespace' => ['Foo\\covariant'];
        yield 'variance keyword leads a namespace' => ['covariant\\Foo'];
        yield 'variance keyword with a hyphen' => ['covariant-x'];
        yield 'keyword leads a namespace' => ['is\\Foo'];
        yield 'keyword with a hyphen' => ['is-x'];
        yield 'keyword in upper case' => ['IS'];

        yield 'fully qualified true' => ['\\true'];
        yield 'fully qualified false' => ['\\false'];
        yield 'fully qualified null' => ['\\NULL'];
        yield 'fully qualified literal word leads a namespace' => ['\\true\\Foo'];
        yield 'literal word leads a namespace' => ['true\\null'];
        yield 'literal word ends a namespace' => ['Foo\\true'];
        yield 'literal word with a hyphen' => ['null-type'];
    }

    #[DataProvider('validNameDataProvider')]
    public function testNameIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidNameDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'two names' => ['Foo Bar'];
        yield 'trailing separator' => ['Foo\\'];
        yield 'trailing separator of a fully qualified' => ['\\Foo\\'];
        yield 'lone separator' => ['\\'];
        yield 'double separator at the start' => ['\\\\Foo'];
        yield 'double separator inside' => ['Foo\\\\Bar'];
        yield 'space around the separator' => ['Foo \\ Bar'];
        yield 'space after the separator' => ['Foo\\ Bar'];
        yield 'space before the separator' => ['Foo \\Bar'];
        yield 'space after the leading separator' => ['\\ Foo'];
        yield 'starts with a digit' => ['1Foo'];
        yield 'starts with a hyphen' => ['-foo'];
        yield 'double hyphen' => ['a--b'];
        yield 'trailing hyphen' => ['Foo-'];
        yield 'trailing hyphen before a separator' => ['Foo-\\Bar'];
        yield 'keyword with a trailing hyphen' => ['is-'];
        yield 'hyphen in a segment' => ['Foo\\Bar-baz'];
        yield 'hyphen in the first segment' => ['non-empty\\Foo'];
        yield 'hyphen in a fully qualified name' => ['\\non-empty'];
        yield 'segment starts with a hyphen' => ['Foo\\-bar'];
        yield 'variable' => ['$that'];
        yield 'variable with a namespace' => ['$this\\Foo'];
        yield 'fully qualified this' => ['\\$this'];
    }

    #[DataProvider('invalidNameDataProvider')]
    public function testNameIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
