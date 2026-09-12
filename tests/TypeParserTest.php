<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\Test;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
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

        self::assertNotSame($parser, $parser->withFeatures(shapes: false));
    }

    #[Test]
    public function withFeaturesDoesNotModifyTheOriginalParser(): void
    {
        $parser = new TypeParser();
        $parser->withFeatures(shapes: false);

        self::assertTrue($parser->features->shapes);
    }

    #[Test]
    public function withFeaturesOverridesTheGivenFeature(): void
    {
        $parser = (new TypeParser())->withFeatures(shapes: false);

        self::assertFalse($parser->features->shapes);
    }

    #[Test]
    public function withFeaturesKeepsTheNonSpecifiedFeatures(): void
    {
        $parser = (new TypeParser(new TypeParserFeatures(literals: false)))
            ->withFeatures(shapes: false);

        self::assertFalse($parser->features->literals);
        self::assertTrue($parser->features->generics);
    }

    #[Test]
    public function withFeaturesAffectsTheParsingBehaviour(): void
    {
        $parser = (new TypeParser())->withFeatures(shapes: false);

        $this->expectException(ParserException::class);

        $parser->parse('array{a: int}');
    }

    #[Test]
    public function theOriginalParserKeepsItsParsingBehaviour(): void
    {
        $parser = new TypeParser();
        $parser->withFeatures(shapes: false);

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
            ->withFeatures(shapes: false);

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
        $type = new NamedTypeNode(Name::createFromString('int'));
        $result = new PartialParsedResult($type, 42);

        self::assertSame($type, $result->type);
        self::assertSame(42, $result->offset);
    }
}
