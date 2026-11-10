<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Snapshot;

use Phplrt\Contracts\Parser\Exception\ParserExceptionInterface as PhplrtParserExceptionInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\Validation\SuccessfulCheckResult;
use TypeLang\Type\Node;

#[Group('snapshot')]
final class AstSnapshotTest extends TestCase
{
    private const UPDATE_VARIABLE = 'TYPELANG_UPDATE_SNAPSHOTS';

    private const ENTRY_TYPE = 'type';

    private const ENTRY_TEMPLATE_PARAMETER = 'template-parameter';

    private const ACCEPTED = 'accepted';

    private const PARTIAL = 'partial';

    private const REJECTED = 'rejected';

    /**
     * @var array<non-empty-string, TypeParser<Node>>
     */
    private static array $parsers = [];

    private static ?NodeDumper $dumper = null;

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string, string}>
     */
    public static function snapshotDataProvider(): iterable
    {
        $isUpdating = (string) \getenv(self::UPDATE_VARIABLE) !== '';

        foreach ([self::ENTRY_TYPE, self::ENTRY_TEMPLATE_PARAMETER] as $entry) {
            $pathnames = \glob(__DIR__ . '/fixtures/' . $entry . '/*.snap');

            \assert($pathnames !== false && $pathnames !== []);

            foreach ($pathnames as $pathname) {
                \assert($pathname !== '');

                $file = new SnapshotFile($pathname);

                if ($isUpdating) {
                    $file->update(static fn(string $source): string => self::describe($entry, $source));
                }

                foreach ($file->getCases() as $line => [$source, $outcome]) {
                    $name = \sprintf(
                        '%s/%s:%d %s',
                        $entry,
                        \basename($pathname),
                        $line,
                        \addcslashes($source, "\0..\37\177..\377"),
                    );

                    yield $name => [$entry, $source, $outcome];
                }
            }
        }
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string, int<0, max>, string}>
     */
    public static function partialSnapshotDataProvider(): iterable
    {
        foreach (self::snapshotDataProvider() as $name => [$entry, $source, $outcome]) {
            if (\preg_match('/^' . self::PARTIAL . '@(\d+) (.+)$/s', $outcome, $matches) === 1) {
                /** @var int<0, max> $offset */
                $offset = (int) $matches[1];

                yield $name => [$entry, $source, $offset, $matches[2]];
            }
        }
    }

    /**
     * @param non-empty-string $entry
     * @return TypeParser<Node>
     */
    private static function getParserOf(string $entry): TypeParser
    {
        return self::$parsers[$entry] ??= match ($entry) {
            self::ENTRY_TYPE => new TypeParser(),
            self::ENTRY_TEMPLATE_PARAMETER => (new TypeParser())->forTemplateParameter(),
            default => throw new \InvalidArgumentException(\sprintf('Unknown parser entry "%s"', $entry)),
        };
    }

    private static function dump(Node $node): string
    {
        self::$dumper ??= new NodeDumper();

        return self::$dumper->dump($node);
    }

    /**
     * @param non-empty-string $entry
     * @return non-empty-string
     * @throws \Throwable in case of the parser fails the way a parser must not
     */
    private static function describe(string $entry, string $source): string
    {
        $parser = self::getParserOf($entry);

        try {
            return self::ACCEPTED . ' ' . self::dump($parser->parse($source));
        } catch (ParserExceptionInterface|PhplrtParserExceptionInterface) {
            $result = $parser->partial($source);
        }

        if ($result instanceof SuccessfulParsedResult) {
            return \sprintf('%s@%d %s', self::PARTIAL, $result->offset, self::dump($result->type));
        }

        return self::REJECTED;
    }

    /**
     * @param non-empty-string $entry
     * @throws \Throwable
     */
    #[DataProvider('snapshotDataProvider')]
    public function testSourceIsReadIntoTheRecordedTree(string $entry, string $source, string $outcome): void
    {
        self::assertSame($outcome, self::describe($entry, $source));
    }

    /**
     * @param non-empty-string $entry
     * @throws \Throwable
     */
    #[DataProvider('snapshotDataProvider')]
    public function testEveryWayOfReadingAgreesOnTheSource(string $entry, string $source, string $outcome): void
    {
        $parser = self::getParserOf($entry);
        $isAccepted = \str_starts_with($outcome, self::ACCEPTED . ' ');

        self::assertSame($isAccepted, $parser->validate($source) instanceof SuccessfulCheckResult);

        $result = $parser->partial($source);

        if (!$isAccepted) {
            self::assertTrue(!$result instanceof SuccessfulParsedResult || $result instanceof PartialParsedResult);

            return;
        }

        self::assertInstanceOf(SuccessfulParsedResult::class, $result);
        self::assertNotInstanceOf(PartialParsedResult::class, $result);
        self::assertSame(\strlen($source), $result->offset);
        self::assertSame($outcome, self::ACCEPTED . ' ' . self::dump($result->type));
    }

    /**
     * @param non-empty-string $entry
     * @param int<0, max> $offset
     * @throws \Throwable
     */
    #[DataProvider('partialSnapshotDataProvider')]
    public function testReadPartOfTheSourceIsAWholeSourceOnItsOwn(
        string $entry,
        string $source,
        int $offset,
        string $tree,
    ): void {
        $node = self::getParserOf($entry)
            ->parse(\substr($source, 0, $offset));

        self::assertSame($tree, self::dump($node));
    }
}
