<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Finder\Finder;
use TypeLang\Parser\Tests\Concern\InteractWithPHPDocParser;
use TypeLang\Parser\Tests\Concern\InteractWithPHPDocTagsParser;
use TypeLang\Parser\Tests\Functional\TestCase;
use TypeLang\PHPDoc\Tag\InvalidTag;
use TypeLang\PHPDoc\Tag\TagInterface;
use TypeLang\PHPDoc\Standard;
use TypeLang\PHPDoc\Tag\TypeProviderInterface;

#[Group('functional'), Group('type-lang/parser')]
abstract class LinterStubsTestCase extends TestCase
{
    use InteractWithPHPDocParser;
    use InteractWithPHPDocTagsParser;

    /**
     * @return non-empty-string
     */
    abstract protected static function getStubsDirectory(): string;

    /**
     * @return iterable<non-empty-string, non-empty-string>
     * @throws \Throwable
     */
    protected static function getVersions(): iterable
    {
        $files = (new Finder())
            ->in(static::getStubsDirectory())
            ->directories()
            ->depth(0);

        foreach ($files as $file) {
            yield $file->getFilename() => $file->getPathname();
        }
    }

    /**
     * @param non-empty-string $directory
     * @return iterable<non-empty-string, non-empty-string>
     * @throws \Throwable
     */
    protected static function getFilesFromDirectory(string $directory): iterable
    {
        $files = (new Finder())
            ->in($directory)
            ->files()
            ->name(['*.php', '*.stub', '*.phpstub', '*.stubphp']);

        foreach ($files as $file) {
            yield $file->getRelativePathname() => $file->getPathname();
        }
    }

    /**
     * @return iterable<non-empty-string, non-empty-string>
     * @throws \Throwable
     */
    protected static function getFiles(): iterable
    {
        foreach (self::getVersions() as $version => $directory) {
            foreach (self::getFilesFromDirectory($directory) as $relative => $pathname) {
                yield $version . '-' . \strtolower($relative) => $pathname;
            }
        }
    }

    /**
     * @return iterable<non-empty-string, TagInterface>
     * @throws ExpectationFailedException
     * @throws \LogicException
     * @throws \Throwable
     */
    protected static function getAllTags(): iterable
    {
        $context = \pathinfo(static::getStubsDirectory(), \PATHINFO_FILENAME);
        $directory = __DIR__ . '/cache/' . $context;

        if (!\is_dir($directory)) {
            \mkdir($directory, recursive: true);
        }

        $types = [];

        foreach (self::getFiles() as $name => $pathname) {
            $cache = $directory . '/' . \str_replace(['/', '\\'], '-', $name) . '.cache';

            // Read from cache
            if (\is_file($cache)) {
                yield from \unserialize(\file_get_contents($cache));
                continue;
            }

            $result = [];
            $docblocks = self::getDocBlocksFromPhpFile($pathname);

            foreach ($docblocks as $info => $docblock) {
                foreach (self::getTagsFromDocBlock($docblock) as $tag) {
                    $result[$name . ' ' . $info] = [$tag];
                }
            }

            yield from $result;

            \file_put_contents($cache, \serialize($result));
        }
    }

    /**
     * @param non-empty-list<non-empty-string> $name
     * @return iterable<non-empty-string, TagInterface>
     * @throws \LogicException
     * @throws ExpectationFailedException
     * @throws \Throwable
     */
    protected static function getTagByName(array $names): iterable
    {
        foreach (self::getAllTags() as $i => [$tag]) {
            if (\in_array($tag->getName(), $names, true)) {
                yield $i => [$tag];
            }
        }
    }

    public static function returnTagDataProvider(): iterable
    {
        yield from self::getTagByName(self::getPrefixedTags('return'));
    }

    #[DataProvider('returnTagDataProvider')]
    public function testReturnStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        self::assertInstanceOf(Standard\ReturnTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    public static function paramTagDataProvider(): iterable
    {
        yield from self::getTagByName([
            ...self::getPrefixedTags('param'),
            ...self::getPrefixedTags('param-out'),
        ]);
    }

    #[DataProvider('paramTagDataProvider')]
    public function testParamStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        $this->inCaseOfReasonPhrase($tag, function (string $message) {
            if (\str_contains($message, 'contains an incorrect variable name')) {
                self::markTestIncomplete('TODO Known phpdoc parser issue: ' . $message);
            }
        });

        self::assertInstanceOf(Standard\ParamTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    public static function varTagDataProvider(): iterable
    {
        yield from self::getTagByName(self::getPrefixedTags('var'));
    }

    #[DataProvider('varTagDataProvider')]
    public function testVarStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        self::assertInstanceOf(Standard\VarTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    public static function propertyTagDataProvider(): iterable
    {
        yield from self::getTagByName([
            'property',
            'property-read',
            'property-write',
        ]);
    }

    #[DataProvider('propertyTagDataProvider')]
    public function testPropertyStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        self::assertInstanceOf(Standard\PropertyTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    public static function methodTagDataProvider(): iterable
    {
        yield from self::getTagByName(['method']);
    }

    #[DataProvider('methodTagDataProvider')]
    public function testMethodStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        self::assertInstanceOf(Standard\MethodTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    public static function throwsTagDataProvider(): iterable
    {
        yield from self::getTagByName(['throws']);
    }

    #[DataProvider('throwsTagDataProvider')]
    public function testThrowsStatementsIsCorrectlyRecognized(TagInterface $tag): void
    {
        self::assertInstanceOf(Standard\ThrowsTag::class, $tag,
            message: $this->getReasonPhrase($tag),
        );
    }

    private function getReasonPhrase(TagInterface $tag): string
    {
        if ($tag instanceof InvalidTag) {
            $reason = $tag->getReason();

            return $reason->getMessage() . ': ' . (string) $tag->getDescription();
        }

        return 'Failed to parse tag: ' . \print_r($tag, true);
    }

    /**
     * @param callable(string):void $then
     */
    private static function inCaseOfReasonPhrase(TagInterface $tag, callable $then): void
    {
        if (!$tag instanceof InvalidTag) {
            return;
        }

        $reason = $tag->getReason();
        $then($reason->getMessage());
    }
}
