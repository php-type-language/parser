<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Parser\Validation\CheckResult;
use TypeLang\Parser\Validation\FailureCheckResult;
use TypeLang\Parser\Validation\PartialCheckResult;
use TypeLang\Parser\Validation\SuccessfulCheckResult;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;

final class TypeParserTest extends TestCase
{
    #[Test]
    public function everyFeatureIsEnabledByDefault(): void
    {
        self::assertEquals(new TypeParserFeatures(), (new TypeParser())->features);
    }

    #[Test]
    public function theFeaturesArePassedThroughTheConstructor(): void
    {
        $features = new TypeParserFeatures(shapes: false);

        self::assertSame($features, (new TypeParser($features))->features);
    }

    #[Test]
    public function withFeaturesReturnsANewInstance(): void
    {
        $parser = new TypeParser();

        self::assertNotSame($parser, $parser->with(shapes: false));
    }

    #[Test]
    public function withFeaturesDoesNotModifyTheOriginalParser(): void
    {
        $parser = new TypeParser();
        $parser->with(shapes: false);

        self::assertTrue($parser->features->shapes);
    }

    #[Test]
    public function withFeaturesOverridesTheGivenFeature(): void
    {
        $parser = (new TypeParser())->with(shapes: false);

        self::assertFalse($parser->features->shapes);
    }

    #[Test]
    public function withFeaturesKeepsTheNonSpecifiedFeatures(): void
    {
        $parser = (new TypeParser(new TypeParserFeatures(literals: false)))
            ->with(shapes: false);

        self::assertFalse($parser->features->literals);
        self::assertTrue($parser->features->generics);
    }

    #[Test]
    public function withFeaturesAffectsTheParsingBehaviour(): void
    {
        $parser = (new TypeParser())->with(shapes: false);

        $this->expectException(ParserException::class);

        $parser->parse('array{a: int}');
    }

    #[Test]
    public function theOriginalParserKeepsItsParsingBehaviour(): void
    {
        $parser = new TypeParser();
        $parser->with(shapes: false);

        self::assertInstanceOf(NamedTypeNode::class, $parser->parse('array{a: int}'));
    }

    #[Test]
    public function theSameParserCanBeUsedSeveralTimes(): void
    {
        $parser = new TypeParser();

        self::assertEquals($parser->parse('int'), $parser->parse('int'));
    }

    #[Test]
    public function theSourceFactoryCanBeOverridden(): void
    {
        $parser = new TypeParser(sources: SourceFactory::createDefault());

        self::assertInstanceOf(NamedTypeNode::class, $parser->parse('int'));
    }

    #[Test]
    public function theSourceFactoryIsInheritedByTheNewInstance(): void
    {
        $parser = (new TypeParser(sources: SourceFactory::createDefault()))
            ->with(shapes: false);

        self::assertInstanceOf(NamedTypeNode::class, $parser->parse('int'));
    }

    #[Test]
    public function aPartialReadingOfAWholeSourceIsASuccessfulOne(): void
    {
        $result = (new TypeParser())->partial('int');

        self::assertInstanceOf(SuccessfulParsedResult::class, $result);
        self::assertNotInstanceOf(PartialParsedResult::class, $result);
        self::assertInstanceOf(NamedTypeNode::class, $result->type);
        self::assertSame('int', $result->type->name->toString());
    }

    #[Test]
    public function theOffsetPointsToTheUnparsedTail(): void
    {
        $source = 'array{ field: result } This is an example';
        $result = (new TypeParser())->partial($source);

        self::assertInstanceOf(PartialParsedResult::class, $result);
        self::assertSame('This is an example', \substr($source, $result->offset));
    }

    #[Test]
    public function aPartialReadingOfNoTypeAtAllIsAFailure(): void
    {
        $result = (new TypeParser())->partial('|int');

        self::assertInstanceOf(FailureParsedResult::class, $result);
    }

    #[Test]
    public function aPartialResultCanBeCreatedManually(): void
    {
        $type = new NamedTypeNode(new Name('int'));
        $result = new PartialParsedResult($type, 42);

        self::assertSame($type, $result->type);
        self::assertSame(42, $result->offset);
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
     * @param non-empty-string $source
     * @param non-empty-string $expected
     * @param int<0, max> $offset
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('tolerantInputDataProvider')]
    public function aPartialReadingStopsAtWhatItCannotRead(string $source, string $expected, int $offset): void
    {
        $result = $this->partial($source);

        self::assertInstanceOf(PartialParsedResult::class, $result);
        self::assertSame($expected, (new PrettyTypePrinter())->print($result->type));
        self::assertSame($offset, $result->offset);
    }

    #[Test]
    public function aPartialReadingOfNoTypeAtAllCarriesTheMessageAndThePosition(): void
    {
        $result = $this->partial('|int');

        self::assertInstanceOf(FailureParsedResult::class, $result);
        self::assertStringContainsString('unexpected "|"', $result->message);
        self::assertSame(0, $result->offset);
        self::assertSame(1, $result->position->line);
        self::assertSame(1, $result->position->column);
    }

    /**
     * @return iterable<non-empty-string, array{string, class-string<CheckResult>}>
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
     * @param class-string<CheckResult> $expected
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('checkedInputDataProvider')]
    public function aCheckTellsWhetherASourceIsAWholeType(string $source, string $expected): void
    {
        self::assertInstanceOf($expected, $this->validate($source));
    }

    #[Test]
    public function aPartialCheckIsAFailureWithTheOffsetOfTheTail(): void
    {
        $result = $this->validate('array{a: int} tail');

        self::assertInstanceOf(FailureCheckResult::class, $result);
        self::assertSame(14, $result->offset);
        self::assertStringContainsString('unexpected "tail"', $result->message);
    }
}
