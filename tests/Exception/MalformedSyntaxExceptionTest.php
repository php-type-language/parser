<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Exception\MalformedSyntaxException;
use TypeLang\Parser\Exception\SyntaxErrorException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Validation\FailureCheckResult;

#[CoversClass(MalformedSyntaxException::class)]
final class MalformedSyntaxExceptionTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function messageDataProvider(): iterable
    {
        yield 'an empty group' => ['()', 'A type is expected after "("'];
        yield 'a group without a type' => ['(|)', 'A type is expected after "("'];
        yield 'an unclosed group' => ['(int', 'The group is not closed properly, ")" expected'];
        yield 'an unclosed group before a token' => ['(int string', 'The group is not closed properly, ")" expected'];

        yield 'an empty argument list' => ['Foo<>', 'A template argument is expected after "<"'];
        yield 'an argument list of a comma' => ['Foo<,>', 'A template argument is expected after "<"'];
        yield 'an argument list with a doubled comma' => ['Foo<int,,string>', 'Invalid template arguments, "," or ">" is expected'];
        yield 'an argument list with a missing comma' => ['Foo<A B>', 'Invalid template arguments, "," or ">" is expected'];
        yield 'an unclosed argument list' => ['Foo<int', 'Invalid template arguments, "," or ">" is expected'];
        yield 'an empty template parameter list' => ['callable<>(): T', 'A template argument is expected after "<"'];
        yield 'an unclosed template parameter list' => ['callable<T', 'Invalid template arguments, "," or ">" is expected'];

        yield 'an offset without a type' => ['T[ }', 'A type or "]" is expected after "["'];
        yield 'an unclosed list suffix' => ['int[', 'A type or "]" is expected after "["'];
        yield 'an unclosed offset' => ['T[int', 'The offset is not closed properly, "]" expected'];
        yield 'an unclosed offset in a shape' => ['A { some: 23[ }', 'A type or "]" is expected after "["'];

        yield 'a field without a type' => ['array{a:}', 'A type is expected after ":"'];
        yield 'a field of a colon and a brace' => ['array{a: }', 'A type is expected after ":"'];
        yield 'a shape of a comma' => ['array{,}', 'Invalid shape, a field, "," or "}" is expected'];
        yield 'an unclosed shape' => ['array{a: int', 'Invalid shape, a field, "," or "}" is expected'];
        yield 'two fields without a comma' => ['array{a: int b}', 'Invalid shape, a field, "," or "}" is expected'];

        yield 'a parameter list of a comma' => ['callable(,)', 'Invalid callable parameters, a parameter, "," or ")" is expected'];
        yield 'an unclosed parameter list' => ['callable(int', 'Invalid callable parameters, a parameter, "," or ")" is expected'];
        yield 'a parameter with a trailing ellipsis' => ['callable(int $a...)', 'Invalid callable parameters, a parameter, "," or ")" is expected'];

        yield 'a condition without an else branch' => ['A is B ? C', 'The condition requires an else branch, ":" expected'];
        yield 'a negated condition without an else branch' => ['A is not B ? C', 'The condition requires an else branch, ":" expected'];
        yield 'a condition without a question mark' => ['A is B', 'A "?" is expected after the target of the condition'];
        yield 'a condition whose target is a question mark' => ['T is ? A : B', 'A "?" is expected after the target of the condition'];
        yield 'a negated condition whose target is a question mark' => ['T is not ? A : B', 'A "?" is expected after the target of the condition'];
    }

    #[Test]
    #[DataProvider('messageDataProvider')]
    public function messageIsTheOneOfTheGrammar(string $source, string $message): void
    {
        self::assertSame($message, $this->parseFailure($source)->getMessage());
    }

    #[Test]
    #[DataProvider('messageDataProvider')]
    public function isMalformedSyntaxException(string $source): void
    {
        self::assertInstanceOf(MalformedSyntaxException::class, $this->parseFailure($source));
    }

    #[Test]
    public function isAnUnexpectedTokenException(): void
    {
        self::assertInstanceOf(UnexpectedTokenException::class, $this->parseFailure('(int'));
    }

    #[Test]
    public function isASyntaxErrorException(): void
    {
        self::assertInstanceOf(SyntaxErrorException::class, $this->parseFailure('(int'));
    }

    #[Test]
    public function rangeStandsOnTheTokenTheReadingBrokeOn(): void
    {
        $range = $this->parseFailure('(int string')->range;

        self::assertSame(5, $range->offset);
        self::assertSame(6, $range->length);
    }

    #[Test]
    public function validationTellsTheSameMessage(): void
    {
        $result = $this->validate('(int string');

        self::assertInstanceOf(FailureCheckResult::class, $result);
        self::assertSame($this->parseFailure('(int string')->getMessage(), $result->message);
    }
}
