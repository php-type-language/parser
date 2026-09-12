<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\Validation\CheckResult;
use TypeLang\Parser\Validation\FailureCheckResult;
use TypeLang\Parser\Validation\PartialCheckResult;
use TypeLang\Parser\Validation\SuccessfulCheckResult;

/**
 * Tests for the input a type cannot be read out of, that is, the one that is
 * empty, the one that is cut short and the one that is punctuation alone.
 */
#[Group('unit'), Group('type-lang/parser')]
final class MalformedInputTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function emptyInputDataProvider(): iterable
    {
        yield 'nothing at all' => [''];
        yield 'spaces' => ['   '];
        yield 'a line terminator' => ["\n"];
        yield 'a tabulation' => ["\t"];
    }

    /**
     * @throws \Throwable
     */
    #[DataProvider('emptyInputDataProvider')]
    public function testAnEmptyInputIsNoType(string $type): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse($type);
    }

    /**
     * A type that is cut short ends where the input does, so the reading
     * stops at the end of it rather than at a token.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function truncatedInputDataProvider(): iterable
    {
        yield 'a condition without its comparand' => ['A is', 'unexpected end of input'];

        // The grammar describes what is missing wherever it can, and the
        // message it carries is reported instead of the token that is absent.
        yield 'a dangling union' => [
            'int|',
            'a union type must carry a type after the vertical bar "|"',
        ];
        yield 'a dangling intersection' => [
            'int&',
            'an intersection type must carry a type after the ampersand "&"',
        ];
        yield 'a question mark alone' => [
            '?',
            'a nullable type must carry the type it makes nullable',
        ];
        yield 'a name that is a separator short' => [
            'Some\\',
            'a name must carry a segment after the separator',
        ];
        yield 'a class constant that is a name short' => [
            'Some::',
            'a class constant must carry a name after the double colon',
        ];
        yield 'an unclosed shape' => ['array{', 'a shape must be closed with a brace "}"'];
        yield 'an unclosed field' => [
            'array{a:',
            'a shape field must carry a type after the colon ":"',
        ];
        yield 'an unclosed argument list' => [
            'Some<',
            'an argument list must carry at least one argument',
        ];
        yield 'an unclosed parameter list' => [
            'callable(',
            'a parameter list must be closed with a bracket ")"',
        ];
        yield 'an unclosed offset' => ['int[', 'an offset must be closed with a bracket "]"'];
        yield 'an unclosed group' => ['(int', 'a group must be closed with a bracket ")"'];
        yield 'a condition without its branches' => [
            'A is B ?',
            'a condition must carry the type it is true of',
        ];
        yield 'a condition of a single branch' => [
            'A is B ? C',
            'a condition must be parted with a colon ":"',
        ];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('truncatedInputDataProvider')]
    public function testATruncatedTypeIsRefused(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function strayTokenDataProvider(): iterable
    {
        yield 'a union delimiter' => ['|', 'unexpected "|"'];
        yield 'an intersection delimiter' => ['&', 'unexpected "&"'];
        yield 'a comma' => [',', 'unexpected ","'];
        yield 'a colon' => [':', 'unexpected ":"'];
        yield 'a double colon' => ['::CONST', 'unexpected "::"'];
        yield 'a closing brace' => ['}', 'unexpected "}"'];
        yield 'a closing bracket' => [']', 'unexpected "]"'];
        yield 'a closing parenthesis' => [')', 'unexpected ")"'];
        yield 'an ellipsis' => ['...', 'unexpected "..."'];
        yield 'an assignment' => ['=', 'unexpected "="'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('strayTokenDataProvider')]
    public function testPunctuationAloneIsNoType(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function trailingTokenDataProvider(): iterable
    {
        yield 'a question mark behind a type' => ['int?', 'unexpected "?"'];
        yield 'an angle bracket too many' => ['Some<int>>', 'unexpected ">"'];
        yield 'a brace too many' => ['array{a: int}}', 'unexpected "}"'];
        yield 'a bracket too many' => ['int[]]', 'unexpected "]"'];
        yield 'an empty argument list' => ['Some<>', 'an argument list must carry at least one argument'];
        yield 'an empty group' => ['()', 'unexpected ")"'];
        yield 'a constant of a generic type' => ['Some<T>::CONST', 'unexpected "::"'];
        yield 'a second type' => ['int string', 'unexpected "string"'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('trailingTokenDataProvider')]
    public function testATokenThatFollowsAWholeTypeIsRefused(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    /**
     * A partial reading keeps whatever type it has read and says where it
     * stopped, rather than refusing the input whole.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, int<0, max>}>
     */
    public static function tolerantInputDataProvider(): iterable
    {
        yield 'a name and a tail' => ['int foo bar', 'int', 4];
        yield 'a union and a tail' => ['int|string extra', 'int|string', 11];
        yield 'a shape and a tail' => ['array{a: int} tail', 'array{a: int}', 14];
        yield 'a list and an ellipsis' => ['int[] ...', 'int[]', 6];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $expected
     * @param int<0, max> $offset
     * @throws \Throwable
     */
    #[DataProvider('tolerantInputDataProvider')]
    public function testAPartialReadingStopsAtWhatItCannotRead(
        string $type,
        string $expected,
        int $offset,
    ): void {
        $result = $this->partial($type);

        self::assertInstanceOf(PartialParsedResult::class, $result);
        self::assertSame($expected, (new \TypeLang\Printer\PrettyTypePrinter())->print($result->type));
        self::assertSame($offset, $result->offset);
    }

    /**
     * A source read in full is no partial one, so it carries no offset of
     * its own.
     */
    public function testAWholeTypeIsNoPartialReading(): void
    {
        $result = $this->partial('array{a: int}');

        self::assertInstanceOf(SuccessfulParsedResult::class, $result);
        self::assertNotInstanceOf(PartialParsedResult::class, $result);
    }

    /**
     * A partial reading is tolerant of a tail alone, so an input that opens
     * no type at all is a failure all the same.
     */
    public function testAPartialReadingRefusesAnInputThatOpensNoType(): void
    {
        $result = $this->partial('|int');

        self::assertInstanceOf(FailureParsedResult::class, $result);
        self::assertStringContainsString('unexpected "|"', $result->message);
        self::assertSame(0, $result->offset);
        self::assertSame(1, $result->position->line);
        self::assertSame(1, $result->position->column);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<CheckResult>}>
     */
    public static function checkedInputDataProvider(): iterable
    {
        yield 'a whole type' => ['array{a: int}', SuccessfulCheckResult::class];
        yield 'a type and a tail' => ['array{a: int} tail', PartialCheckResult::class];
        yield 'no type at all' => ['|int', FailureCheckResult::class];
        yield 'an empty source' => ['', FailureCheckResult::class];
    }

    /**
     * A check builds nothing, so it says what stands in the way and nothing
     * else.
     *
     * @param non-empty-string $type
     * @param class-string<CheckResult> $expected
     * @throws \Throwable
     */
    #[DataProvider('checkedInputDataProvider')]
    public function testACheckTellsWhetherASourceIsAWholeType(string $type, string $expected): void
    {
        self::assertInstanceOf($expected, $this->validate($type));
    }

    /**
     * A source read in part is a failure of a check, since a check asks
     * about the source whole.
     */
    public function testAPartialCheckIsAFailure(): void
    {
        $result = $this->validate('array{a: int} tail');

        self::assertInstanceOf(FailureCheckResult::class, $result);
        self::assertSame(14, $result->offset);
        self::assertStringContainsString('unexpected "tail"', $result->message);
    }
}
