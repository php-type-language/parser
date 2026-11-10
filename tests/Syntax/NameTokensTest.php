<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A word, a virtual name, a namespace and a mask are tokens of their own
 * (grammar/lexemes.pp3).
 */
final class NameTokensTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function singleTokenDataProvider(): iterable
    {
        yield 'word' => ['Foo', SyntaxChecker::T_IDENTIFIER];
        yield 'word with a suffix of a keyword' => ['isFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'word in upper case is not a keyword' => ['IS', SyntaxChecker::T_IDENTIFIER];
        yield 'word of a literal with a suffix' => ['trueFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'multibyte word' => ['Фу', SyntaxChecker::T_IDENTIFIER];

        yield 'virtual name' => ['non-empty-string', SyntaxChecker::T_VIRTUAL_NAME];
        yield 'virtual name of two words' => ['class-string', SyntaxChecker::T_VIRTUAL_NAME];
        yield 'virtual name in upper case' => ['Non-Empty', SyntaxChecker::T_VIRTUAL_NAME];
        yield 'virtual name with a digit' => ['int-0', SyntaxChecker::T_VIRTUAL_NAME];

        yield 'keyword is' => ['is', SyntaxChecker::T_IS];
        yield 'keyword not' => ['not', SyntaxChecker::T_NOT];
        yield 'keyword of' => ['of', SyntaxChecker::T_OF];
        yield 'keyword as' => ['as', SyntaxChecker::T_AS];
        yield 'keyword super' => ['super', SyntaxChecker::T_SUPER];
        yield 'keyword covariant' => ['covariant', SyntaxChecker::T_COVARIANT];
        yield 'keyword contravariant' => ['contravariant', SyntaxChecker::T_CONTRAVARIANT];
        yield 'keyword invariant' => ['invariant', SyntaxChecker::T_INVARIANT];
        yield 'keyword covariant in upper case is not a keyword' => ['COVARIANT', SyntaxChecker::T_IDENTIFIER];
        yield 'keyword covariant with a suffix' => ['covariantFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'keyword of with a suffix' => ['offset', SyntaxChecker::T_IDENTIFIER];
        yield 'keyword as with a suffix' => ['asset', SyntaxChecker::T_IDENTIFIER];
        yield 'keyword super with a suffix' => ['superFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'keyword super in upper case is not a keyword' => ['SUPER', SyntaxChecker::T_IDENTIFIER];

        yield 'true' => ['true', SyntaxChecker::T_TRUE];
        yield 'false in upper case' => ['FALSE', SyntaxChecker::T_FALSE];
        yield 'null' => ['null', SyntaxChecker::T_NULL];
    }

    #[DataProvider('singleTokenDataProvider')]
    public function testWordIsOneToken(string $source, int $expected): void
    {
        self::assertSame([$expected], (new SyntaxChecker())->tokenize($source));
    }

    /**
     * A namespace is a token of its own, the word behind it is the next one.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<int>}>
     */
    public static function namespaceTokensDataProvider(): iterable
    {
        $namespace = SyntaxChecker::T_NAMESPACE;
        $word = SyntaxChecker::T_IDENTIFIER;

        yield 'qualified' => ['Foo\\Bar', [$namespace, $word]];
        yield 'fully qualified' => ['\\Foo', [$namespace, $word]];
        yield 'fully qualified and qualified' => ['\\Foo\\Bar\\Baz', [$namespace, $word]];
        yield 'multibyte' => ['Фу\\Бар', [$namespace, $word]];
        yield 'keyword leads a namespace' => ['is\\Foo', [$namespace, $word]];
        yield 'keyword in a namespace' => ['Foo\\is', [$namespace, SyntaxChecker::T_IS]];
        yield 'fully qualified keyword' => ['\\is', [$namespace, SyntaxChecker::T_IS]];
        yield 'literal word leads a namespace' => ['true\\null', [$namespace, SyntaxChecker::T_NULL]];
        yield 'literal word ends a namespace' => ['Foo\\null', [$namespace, SyntaxChecker::T_NULL]];
        yield 'fully qualified literal word leads a namespace' => ['\\true\\Foo', [$namespace, $word]];
        yield 'fully qualified true' => ['\\true', [$namespace, SyntaxChecker::T_TRUE]];
        yield 'fully qualified false in upper case' => ['\\FALSE', [$namespace, SyntaxChecker::T_FALSE]];
        yield 'fully qualified null' => ['\\null', [$namespace, SyntaxChecker::T_NULL]];
        yield 'namespace keyword' => ['namespace\\Foo', [$namespace, $word]];
        yield 'virtual name behind a namespace' => ['Foo\\non-empty', [$namespace, SyntaxChecker::T_VIRTUAL_NAME]];
    }

    /**
     * @param list<int> $expected
     */
    #[DataProvider('namespaceTokensDataProvider')]
    public function testNamespaceIsATokenOfItsOwn(string $source, array $expected): void
    {
        self::assertSame($expected, (new SyntaxChecker())->tokenize($source));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, list<int>}>
     */
    public static function neighbourTokensDataProvider(): iterable
    {
        $colons = SyntaxChecker::T_DOUBLE_COLON;

        $star = SyntaxChecker::T_WILDCARD;
        $mask = SyntaxChecker::T_IDENTIFIER_MASK;
        $namespace = SyntaxChecker::T_NAMESPACE;
        $word = SyntaxChecker::T_IDENTIFIER;

        yield 'global mask' => ['PHP_*', [$mask]];
        yield 'global mask of a namespaced name' => ['Foo\\BAR_*', [$namespace, $mask]];
        yield 'mask with a prefix' => ['*_SUFFIX', [$mask]];
        yield 'mask of a namespace' => ['Path\\To\\*', [$namespace, $star]];
        yield 'mask of a one word namespace' => ['Path\\*', [$namespace, $star]];
        yield 'mask of a fully qualified namespace' => ['\\Path\\To\\*', [$namespace, $star]];
        yield 'mask of the root' => ['\\*', [$namespace, $star]];
        yield 'mask of the root with a suffix' => ['\\*_SUFFIX', [$namespace, $mask]];
        yield 'mask with the stars between words' => ['A*B*C', [$mask]];
        yield 'mask of a keyword with a star' => ['true*', [$mask]];
        yield 'two stars in a row are not a mask' => ['A**', [$mask, $star]];
        yield 'lone star' => ['*', [$star]];
        yield 'mask behind the colons' => ['Foo::BAR_*', [$word, $colons, $mask]];
        yield 'star behind the colons' => ['Foo::*', [$word, $colons, $star]];
        yield 'constant' => ['Foo::BAR', [$word, $colons, $word]];
        yield 'constant named like a keyword' => ['Foo::is', [$word, $colons, SyntaxChecker::T_IS]];
        yield 'constant named like a literal' => ['Foo::NULL', [$word, $colons, SyntaxChecker::T_NULL]];
        yield 'constant with a namespace' => ['Foo::Bar\\Baz', [$word, $colons, $namespace, $word]];
        yield 'constant with a leading separator' => ['Foo::\\BAR', [$word, $colons, $namespace, $word]];
        yield 'class with a namespace' => ['\\Foo\\Bar::BAZ', [$namespace, $word, $colons, $word]];
        yield 'shape key named like a literal' => [
            'array{true: int}',
            [
                $word,
                SyntaxChecker::T_BRACE_OPEN,
                SyntaxChecker::T_TRUE,
                SyntaxChecker::T_COLON,
                $word,
                SyntaxChecker::T_BRACE_CLOSE,
            ],
        ];
        yield 'space before a namespace' => ['Foo \\Bar', [$word, $namespace, $word]];
        yield 'two words' => ['Foo Bar', [$word, $word]];
    }

    /**
     * @param list<int> $expected
     */
    #[DataProvider('neighbourTokensDataProvider')]
    public function testWordKeepsToItsKind(string $source, array $expected): void
    {
        self::assertSame($expected, (new SyntaxChecker())->tokenize($source));
    }

    /**
     * A separator that no word or mask follows right behind it is not a part of a namespace.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function notANamespaceDataProvider(): iterable
    {
        yield 'trailing separator' => ['Foo\\'];
        yield 'separator alone' => ['\\'];
        yield 'space behind the separator' => ['Foo\\ Bar'];
        yield 'separator behind the separator' => ['Foo\\\\Bar'];
        yield 'number behind the separator' => ['Foo\\1'];
        yield 'variable behind the separator' => ['Foo\\$this'];
    }

    #[DataProvider('notANamespaceDataProvider')]
    public function testSeparatorWithNothingBehindIsNotANamespace(string $source): void
    {
        self::assertNotContains(SyntaxChecker::T_NAMESPACE, (new SyntaxChecker())->tokenize($source));
    }
}
