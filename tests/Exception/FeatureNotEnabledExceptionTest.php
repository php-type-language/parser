<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\ParsingException;
use TypeLang\Parser\Tests\TestCase;

#[CoversClass(FeatureNotEnabledException::class)]
final class FeatureNotEnabledExceptionTest extends TestCase
{
    private function createException(): FeatureNotEnabledException
    {
        return FeatureNotEnabledException::becauseFeatureIsNotEnabled(
            feature: 'literals',
            source: SourceFactory::createDefault()->create('int|"a"'),
            offset: 4,
            length: 3,
        );
    }

    #[Test]
    public function isAParsingException(): void
    {
        self::assertInstanceOf(ParsingException::class, $this->createException());
    }

    #[Test]
    public function isAParserException(): void
    {
        self::assertInstanceOf(ParserExceptionInterface::class, $this->createException());
    }

    #[Test]
    public function messageNamesTheFeature(): void
    {
        self::assertSame(
            'Literals feature is not allowed',
            $this->createException()->getMessage(),
        );
    }

    #[Test]
    public function rangePointsToTheUsedSyntax(): void
    {
        $range = $this->createException()->range;

        self::assertSame(4, $range->offset);
        self::assertSame(3, $range->length);
    }

    #[Test]
    public function keepsTheSource(): void
    {
        self::assertSame('int|"a"', $this->createException()->source->content);
    }
}
