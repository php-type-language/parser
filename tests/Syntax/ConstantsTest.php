<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Constants of classes, global constants and their masks (grammar/constants.pp3).
 */
final class ConstantsTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validConstantDataProvider(): iterable
    {
        yield 'constant' => ['Foo::BAR'];
        yield 'constant in lower case' => ['Foo::bar'];
        yield 'constant with a namespace' => ['Foo\\Bar::BAZ'];
        yield 'constant with a fully qualified class' => ['\\Foo\\Bar::BAZ'];
        yield 'constant of self' => ['self::BAR'];
        yield 'constant of static' => ['static::BAR'];
        yield 'constant of parent' => ['parent::BAR'];
        yield 'class name' => ['Foo::class'];
        yield 'class name of static' => ['static::class'];
        yield 'literal word as a constant' => ['Foo::true'];
        yield 'keyword as a constant' => ['Foo::is'];
        yield 'builtin as a class' => ['int::BAR'];
        yield 'spaces around the colons' => ['Foo :: BAR'];
        yield 'comment behind the colons' => ['Foo::/* c */BAR'];
        yield 'spaces behind a constant' => ['Foo::BAR  '];
        yield 'comment behind a constant' => ['Foo::BAR // c'];
        yield 'spaces behind a star' => ['Foo::* '];
        yield 'spaces behind a mask' => ['Foo::A*B '];
        yield 'digit behind a star' => ['Foo::A*1'];
        yield 'digits behind a star' => ['Foo::A_*_1_2'];

        yield 'mask of a class' => ['Foo::*'];
        yield 'mask with a suffix' => ['Foo::BAR_*'];
        yield 'mask with a prefix' => ['Foo::*_BAZ'];
        yield 'mask inside' => ['Foo::B*R'];
        yield 'mask with two stars' => ['Foo::A*B*'];
        yield 'mask on both ends' => ['Foo::*BAR*'];
        yield 'mask with three words' => ['Foo::A*B*C'];
        yield 'mask of a fully qualified class' => ['\\Foo::BAR_*'];
        yield 'mask of self' => ['self::*'];
        yield 'mask of a keyword' => ['Foo::is*'];

        yield 'global constant' => ['PHP_EOL'];
        yield 'namespaced global constant' => ['Foo\\BAR'];
    }

    #[DataProvider('validConstantDataProvider')]
    public function testConstantIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validGlobalMaskDataProvider(): iterable
    {
        yield 'global mask' => ['PHP_*'];
        yield 'global mask with a prefix' => ['*_SUFFIX'];
        yield 'global mask inside' => ['PHP_*_MAX'];
        yield 'global mask on both ends' => ['*A*'];
        yield 'global mask of three words' => ['A*B*C'];
        yield 'global mask of a word and a star' => ['Foo*'];
        yield 'global mask of a keyword' => ['is*'];
        yield 'fully qualified global mask' => ['\\PHP_*'];
        yield 'fully qualified root' => ['\\*'];
        yield 'fully qualified root with a suffix' => ['\\*_SUFFIX'];
        yield 'namespaced global mask' => ['\\Foo\\BAR_*'];
        yield 'namespaced mask without a prefix' => ['Foo\\Bar\\BAZ_*'];
        yield 'mask of a whole namespace' => ['Path\\To\\*'];
        yield 'mask of a whole one word namespace' => ['Path\\*'];
        yield 'mask with a suffix in a namespace' => ['Path\\To\\*_SUFFIX'];
        yield 'mask of a whole fully qualified namespace' => ['\\Path\\To\\*'];
        yield 'mask with words in a namespace' => ['Path\\To\\*A*B'];
        yield 'spaces behind a mask' => ['PHP_*  '];
        yield 'comment behind a mask' => ['PHP_* // c'];
        yield 'spaces before a mask' => ['  *_SUFFIX'];
        yield 'mask of a literal word' => ['true*'];
        yield 'mask of null' => ['null*'];
        yield 'mask of a keyword' => ['is_*'];
    }

    #[DataProvider('validGlobalMaskDataProvider')]
    public function testGlobalMaskIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidConstantDataProvider(): iterable
    {
        // A part of the constant is missing
        yield 'no constant' => ['Foo::'];
        yield 'no constant, spaces behind' => ['Foo ::  '];
        yield 'no class' => ['::BAR'];
        yield 'no class, a mask' => ['::*'];
        yield 'colons alone' => ['::'];
        yield 'class behind a separator only' => ['Foo\\::BAR'];
        yield 'leading separator only' => ['\\::BAR'];

        // The colons are not a pair of them
        yield 'one colon' => ['Foo:BAR'];
        yield 'three colons' => ['Foo:::BAR'];
        yield 'four colons' => ['Foo::::BAR'];
        yield 'colons apart' => ['Foo: :BAR'];
        yield 'colon behind a mask' => ['Foo::*:'];
        yield 'colon behind a constant' => ['Foo::BAR:'];

        // More than one pair of colons
        yield 'constant of a constant' => ['Foo::BAR::BAZ'];
        yield 'constant of a mask' => ['Foo::*::BAR'];
        yield 'colons behind a constant' => ['Foo::BAR::'];
        yield 'two constants in a row' => ['Foo::BAR Foo::BAZ'];

        // The words and the stars do not take turns
        yield 'two stars in a row' => ['Foo::**'];
        yield 'two stars in a row inside' => ['Foo::A**B'];
        yield 'two stars in a row behind a word' => ['Foo::BAR**'];
        yield 'two stars in a row before a word' => ['Foo::**BAR'];
        yield 'two stars apart' => ['Foo::* *'];
        yield 'two stars apart by a comment' => ['Foo::*/* c */*'];
        yield 'two words' => ['Foo::A B'];
        yield 'two words behind a star' => ['Foo::*A B'];
        yield 'two words before a star' => ['Foo::A B*'];
        yield 'two words inside a mask' => ['Foo::A*B C*D'];
        yield 'three stars' => ['Foo::A***'];

        // A part of the constant is not a word or a star
        yield 'constant starts with a digit' => ['Foo::1'];
        yield 'constant is a hexadecimal' => ['Foo::0x1F'];
        yield 'constant is a float' => ['Foo::1.5'];
        yield 'constant is a string' => ['Foo::"BAR"'];
        yield 'constant is a single quoted string' => ["Foo::'BAR'"];
        yield 'constant is a variable' => ['Foo::$bar'];
        yield 'constant is this' => ['Foo::$this'];
        yield 'constant with a namespace' => ['Foo::Bar\\Baz'];
        yield 'constant with a leading separator' => ['Foo::\\BAR'];
        yield 'literal word with a leading separator as a constant' => ['Foo::\\null'];
        yield 'namespaced word inside a mask' => ['Foo::A*Bar\\Baz'];
        yield 'mask with a separator inside' => ['Foo::A*\\B'];

        // Something that is not a part of a constant follows it
        yield 'bar behind a constant' => ['Foo::BAR|'];
        yield 'comma behind a constant' => ['Foo::BAR,'];
        yield 'question mark behind a constant' => ['Foo::BAR?'];
        yield 'ampersand behind a constant' => ['Foo::BAR&'];
        yield 'bracket behind a constant' => ['Foo::BAR)'];
        yield 'square bracket behind a constant' => ['Foo::BAR]'];
        yield 'brace behind a constant' => ['Foo::BAR}'];
        yield 'angle bracket behind a constant' => ['Foo::BAR>'];
        yield 'ellipsis behind a constant' => ['Foo::BAR...'];
        yield 'name behind a constant' => ['Foo::BAR Foo'];
        yield 'number behind a constant' => ['Foo::BAR 1'];
        yield 'variable behind a constant' => ['Foo::BAR $x'];

        // The class is not a name
        yield 'class is this' => ['$this::BAR'];
        yield 'class is a variable' => ['$foo::BAR'];
        yield 'class is a number' => ['1::BAR'];
        yield 'class is a string' => ['"Foo"::BAR'];
        yield 'class is a literal' => ['true::BAR'];
        yield 'class is null' => ['null::BAR'];
        yield 'class is a star' => ['*::BAR'];
        yield 'class is a mask' => ['Foo*::BAR'];
        yield 'class is a mask with a word' => ['*Foo::BAR'];

        // A global mask that is not one
        yield 'two stars behind a word' => ['PHP_**'];
        yield 'two stars before a word' => ['**A'];
        yield 'two stars in a namespace' => ['Path\\To\\**'];
        yield 'two stars in the root' => ['\\**'];
        yield 'namespace with no star' => ['Path\\To\\'];
        yield 'star behind a star of a namespace' => ['Path\\To\\*\\*'];
        yield 'two namespaces' => ['Path\\*\\To\\*'];
        yield 'separators in a row before a star' => ['\\\\*'];
        yield 'separator alone' => ['\\'];
        yield 'separator and a space before a star' => ['\\ *'];
        yield 'constant of a global mask' => ['PHP_*::X'];
        yield 'constant of a namespace mask' => ['Path\\To\\*::X'];
        yield 'constant of the root' => ['\\*::X'];
        yield 'two masks' => ['A* B*C D'];
        yield 'bar behind a mask' => ['PHP_*|'];
        yield 'colon behind a global mask' => ['PHP_*:'];
        yield 'number with a star' => ['1*'];
        yield 'string with a star' => ['"s"*'];
        yield 'this with a star' => ['$this*'];
        yield 'variable with a star' => ['$foo*'];

        // A constant has no hyphens, only a virtual type has
        yield 'hyphen in a constant' => ['Foo::BAR-BAZ'];
        yield 'virtual name as a constant' => ['Foo::non-empty'];
        yield 'hyphen in a mask of a class' => ['Foo::BAR-*'];
        yield 'hyphen in a global mask' => ['PHP-*'];
        yield 'hyphen behind a star' => ['*-SUFFIX'];
        yield 'hyphen in a namespace of a mask' => ['Foo-Bar\\BAZ_*'];

        // A mask is one token, there is no room for a space in it
        yield 'spaces inside a mask of a class' => ['Foo::A * B'];
        yield 'space before a star' => ['PHP_ *'];
        yield 'space behind a star' => ['PHP_* MAX'];
        yield 'spaces around a star' => ['PHP_ * MAX'];
        yield 'comment inside a mask' => ['PHP_/* c */*'];
        yield 'space in a namespaced mask' => ['Path\\To\\ *'];

        // A mask or a star alone is not a type until it has a class or a star alone is not a type until it has a class
        yield 'lone star' => ['*'];
        yield 'two stars' => ['**'];
        yield 'two stars apart alone' => ['* *'];
        yield 'two stars behind a name' => ['A**'];

        // A comment that is not closed swallows the rest
        yield 'unclosed comment behind a constant' => ['Foo::BAR /*'];
        yield 'unclosed comment instead of a constant' => ['Foo::/* BAR'];
    }
    #[DataProvider('invalidConstantDataProvider')]
    public function testConstantIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
