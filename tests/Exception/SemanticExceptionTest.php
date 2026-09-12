<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\FeatureNotAllowedException;
use TypeLang\Parser\Exception\InternalSemanticException;
use TypeLang\Parser\Exception\InvalidConditionalOperatorException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\ParsingException;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\ShapeFieldDuplicationException;
use TypeLang\Parser\Exception\ShapeKeysMixingException;
use TypeLang\Parser\Exception\VariadicWithDefaultException;
use TypeLang\Parser\Tests\TestCase;

final class SemanticExceptionTest extends TestCase
{
    private static function source(string $statement = 'array{a: int, a: int}'): ReadableInterface
    {
        return SourceFactory::createDefault()->create($statement);
    }

    /**
     * @return iterable<non-empty-string, array{\Closure(ReadableInterface,int):SemanticException}>
     */
    public static function factoryDataProvider(): iterable
    {
        yield 'feature not allowed' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => FeatureNotAllowedException::becauseFeatureIsNotAllowed('shapes', $source, $offset),
        ];

        yield 'unexpected sub-node' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => InternalSemanticException::becauseSubNodeIsUnexpected('Example', $source, $offset),
        ];

        yield 'invalid conditional operator' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => InvalidConditionalOperatorException::becauseConditionalOperatorIsInvalid('~', $source, $offset),
        ];

        yield 'shape field duplication' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('key', $source, $offset),
        ];

        yield 'shape keys mixing' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => ShapeKeysMixingException::becauseShapeKeysAreMixed($source, $offset),
        ];

        yield 'variadic with default' => [
            static fn(ReadableInterface $source, int $offset): SemanticException
                => VariadicWithDefaultException::becauseVariadicHasDefault($source, $offset),
        ];
    }

    /**
     * @param \Closure(ReadableInterface,int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionIsAParseError(\Closure $factory): void
    {
        $exception = $factory(self::source(), 0);

        self::assertInstanceOf(ParserExceptionInterface::class, $exception);
        self::assertInstanceOf(ParsingException::class, $exception);
    }

    /**
     * @param \Closure(ReadableInterface,int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionKeepsTheSourceAndThePlace(\Closure $factory): void
    {
        $source = self::source();
        $exception = $factory($source, 14);

        self::assertSame($source, $exception->source);
        self::assertSame(14, $exception->token->offset);
        self::assertSame(14, $exception->getOffset());
    }

    /**
     * @param \Closure(ReadableInterface,int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionCarriesTheSourceInItsMessage(\Closure $factory): void
    {
        $exception = $factory(self::source(), 0);

        self::assertStringEndsWith('in "array{a: int, a: int}"', $exception->getMessage());
    }

    /**
     * @param \Closure(ReadableInterface,int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionIsPrintedWithItsPlace(\Closure $factory): void
    {
        $exception = $factory(self::source(), 14);

        self::assertStringContainsString('on line 1 at column 15', (string) $exception);
    }

    #[Test]
    public function theFeatureNameIsCapitalizedInTheMessage(): void
    {
        $exception = FeatureNotAllowedException::becauseFeatureIsNotAllowed('shape fields', self::source());

        self::assertStringStartsWith('Shape fields not allowed', $exception->getMessage());
    }

    #[Test]
    public function theUnexpectedSubNodeIsReported(): void
    {
        $exception = InternalSemanticException::becauseSubNodeIsUnexpected('Example', self::source());

        self::assertStringStartsWith(
            'Internal error, unexpected square bracket sub-node Example',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function theInvalidConditionalOperatorIsReported(): void
    {
        $exception = InvalidConditionalOperatorException::becauseConditionalOperatorIsInvalid('~', self::source());

        self::assertStringStartsWith('Invalid conditional operator "~"', $exception->getMessage());
    }

    #[Test]
    public function theDuplicatedShapeFieldIsReported(): void
    {
        $exception = ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('key', self::source());

        self::assertStringStartsWith('Duplicate key "key"', $exception->getMessage());
    }

    #[Test]
    public function theMixedShapeKeysAreReported(): void
    {
        $exception = ShapeKeysMixingException::becauseShapeKeysAreMixed(self::source());

        self::assertStringStartsWith('Cannot mix explicit and implicit shape keys', $exception->getMessage());
    }

    #[Test]
    public function theVariadicWithADefaultIsReported(): void
    {
        $exception = VariadicWithDefaultException::becauseVariadicHasDefault(self::source());

        self::assertStringStartsWith('Cannot have variadic param with a default', $exception->getMessage());
    }

    /**
     * @param \Closure(ReadableInterface,int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionIsToldApartByItsClassAlone(\Closure $factory): void
    {
        self::assertSame(0, $factory(self::source(), 0)->getCode());
    }
}
