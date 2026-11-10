<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
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
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateParameterNode;

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

    /**
     * Returns every node of the tree, indexed by the identifiers of the
     * objects.
     *
     * @return array<int, Node>
     */
    private static function collectNodes(mixed $value): array
    {
        if (\is_array($value)) {
            $result = [];

            foreach ($value as $item) {
                $result += self::collectNodes($item);
            }

            return $result;
        }

        if (!$value instanceof Node) {
            return [];
        }

        return [\spl_object_id($value) => $value] + self::collectNodes(\get_object_vars($value));
    }

    #[Test]
    public function everyReadingReturnsATreeOfItsOwn(): void
    {
        $parser = new TypeParser();
        $source = 'array<int, Foo|Bar>|callable(int $a = 42): array{a: T is U ? 1 : \'a\'}';

        $first = $parser->parse($source);
        $second = $parser->parse($source);

        $nodes = self::collectNodes($first);

        self::assertEquals($first, $second);
        self::assertGreaterThan(20, \count($nodes));
        self::assertSame([], \array_intersect_key($nodes, self::collectNodes($second)));
    }

    #[Test]
    public function aChangedTreeDoesNotAffectTheNextReading(): void
    {
        $parser = new TypeParser();
        $expected = $parser->parse('array<int>');

        $changed = $parser->parse('array<int>');

        self::assertInstanceOf(NamedTypeNode::class, $changed);

        $changed->name = new Name('changed');
        $changed->arguments = null;

        self::assertEquals($expected, $parser->parse('array<int>'));
    }

    #[Test]
    public function theParserReadsASourceAfterARefusedOne(): void
    {
        $parser = new TypeParser();
        $expected = $parser->parse('array<int>|Foo');

        try {
            $parser->parse('array<int');

            self::fail('The source must not be parsed');
        } catch (ParserExceptionInterface) {
            // The refusal is what the reading below is preceded by
        }

        self::assertInstanceOf(PartialParsedResult::class, $parser->partial('int $a'));
        self::assertInstanceOf(FailureCheckResult::class, $parser->validate('int|'));
        self::assertEquals($expected, $parser->parse('array<int>|Foo'));
    }

    #[Test]
    public function theParsersOfDifferentFeaturesDoNotAffectEachOther(): void
    {
        $parser = new TypeParser();
        $restricted = $parser->with(shapes: false);

        for ($i = 0; $i < 2; ++$i) {
            self::assertInstanceOf(NamedTypeNode::class, $parser->parse('array{a: int}'));

            try {
                $restricted->parse('array{a: int}');

                self::fail('The source must not be parsed');
            } catch (FeatureNotEnabledException) {
                // The refusal is what the next reading is preceded by
            }
        }
    }

    #[Test]
    public function theTemplateParameterParserReadsATemplateParameter(): void
    {
        $parser = (new TypeParser())->forTemplateParameter();

        self::assertInstanceOf(TemplateParameterNode::class, $parser->parse('T of int = 42'));
    }

    #[Test]
    public function theTemplateParameterParserDoesNotAffectTheTypeParser(): void
    {
        $types = new TypeParser();
        $types->parse('int');

        $parameters = $types->forTemplateParameter();

        self::assertNotSame($types, $parameters);
        self::assertInstanceOf(TemplateParameterNode::class, $parameters->parse('T of int'));
        self::assertInstanceOf(NamedTypeNode::class, $types->parse('T'));
        self::assertInstanceOf(PartialParsedResult::class, $types->partial('T of int'));
        self::assertInstanceOf(TemplateParameterNode::class, $parameters->parse('T'));
    }

    #[Test]
    public function theTypeParserIsReturnedBackFromTheTemplateParameterOne(): void
    {
        $parser = (new TypeParser())
            ->forTemplateParameter()
            ->forType();

        self::assertInstanceOf(NamedTypeNode::class, $parser->parse('T'));
        self::assertInstanceOf(PartialParsedResult::class, $parser->partial('T of int'));
    }

    #[Test]
    public function theTemplateParameterParserKeepsTheFeatures(): void
    {
        $parser = (new TypeParser(new TypeParserFeatures(shapes: false)))
            ->forTemplateParameter();

        self::assertFalse($parser->features->shapes);
        self::assertInstanceOf(TemplateParameterNode::class, $parser->parse('T of int'));

        $this->expectException(FeatureNotEnabledException::class);

        $parser->parse('T of array{a: int}');
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
        self::assertStringContainsString('Unexpected "|"', $result->message);
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
        self::assertStringContainsString('Unexpected "tail"', $result->message);
    }
}
