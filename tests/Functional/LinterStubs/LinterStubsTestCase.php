<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\FileInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Position\Position;
use Phplrt\Source\File;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\Concern\InteractWithDocBlocks;
use TypeLang\Parser\Tests\Functional\TestCase;

abstract class LinterStubsTestCase extends TestCase
{
    use InteractWithDocBlocks;

    protected const TAGS = [
        'param', 'var', 'return',
        'phpstan-param', 'phpstan-var', 'phpstan-return',
        'psalm-param', 'psalm-var', 'psalm-return',
    ];

    /**
     * @psalm-taint-sink file $directory
     * @param non-empty-string $directory
     *
     * @return iterable<array-key, ReadableInterface>
     */
    protected static function getSources(string $directory, array $ext = ['php', 'stub', 'phpstub', 'stubphp']): iterable
    {
        $result = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($result as $file) {
            if (!\in_array(\strtolower($file->getExtension()), $ext, true)) {
                continue;
            }

            yield File::fromSplFileInfo($file);
        }
    }

    abstract protected static function getCachePathname(): string;

    abstract protected static function getFilesDirectory(): string;

    /**
     * @return array<array-key, array{non-empty-string, non-empty-string}>
     */
    public static function dataProvider(): array
    {
        if (!\is_file(static::getCachePathname())) {
            $result = [];

            $blocks = self::getDocTypesFromSources(
                sources: self::getSources(static::getFilesDirectory()),
                tags: self::TAGS,
            );

            foreach ($blocks as $phpdoc => [$file, $position]) {
                $result[$phpdoc] = [$phpdoc, self::location($file, $position)];
            }

            \file_put_contents(
                filename: static::getCachePathname(),
                data: '<?php return ' . \var_export($result, true) . ';',
            );
        }

        return require static::getCachePathname();
    }

    protected static function location(ReadableInterface $src, PositionInterface $pos = null): string
    {
        $name = '<source#' . $src->getHash() . '>';

        if ($src instanceof FileInterface) {
            $name = \realpath($src->getPathname()) ?: $src->getPathname();
        }

        return $name . ':' . ($pos ?? Position::start())->getLine();
    }

    #[DataProvider('dataProvider')]
    public function testStubs(string $type, string $location): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->getStatementResult($type);
        } catch (\Throwable $e) {
            $this->onFail($e, $type, $location);
        }
    }

    protected function onFail(\Throwable $e, string $expr, string $location): void
    {
        $message = $e::class . ': ' . $e->getMessage()
            . "\n  - Definition: $expr"
            . "\n  - Source:     $location"
        ;

        //
        // Known errors in phpdoc
        //
        if (
            // Invalid phpdoc, like a "Iterator<<string>>"
            \str_contains($expr, '>>') ||
            // Psr\EventDispatcher\ListenerProviderInterface phpdoc bug
            \str_contains($expr, 'iterable[callable]') ||
            // Non-const expression in typedef (will not support)
            \str_contains($expr, 'func_num_args() > ') ||
            // PHPDoc bug: https://github.com/phpDocumentor/ReflectionDocBlock/issues/351
            \str_ends_with($expr, 'string[]}>}|array}|null') ||
            // Invalid stmts
            \str_contains($expr, 'array[string]') ||
            // phpstan bug in PHPUnit\Framework\Constraint\IsType:124
            // Cannot extract 'resource'|'resource (closed)' expressions.
            \str_ends_with($expr, "'resource'|'resource")
        ) {
            $this->markTestIncomplete("Test is flagged as false-positive:\n" . $message);
        }

        //
        // Known issues
        //
        if (
            // Conditional types with variables not supported
            \str_starts_with($e->getMessage(), 'Syntax error, unexpected "$') ||
            // Conditional types with template params not supported
            \str_starts_with($e->getMessage(), 'Syntax error, unexpected "is"')
        ) {
            $this->markTestIncomplete("Test is flagged as a known issue:\n" . $message);
        }

        $this->fail($message);
    }
}
