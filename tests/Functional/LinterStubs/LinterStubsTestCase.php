<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

use Composer\Autoload\ClassLoader;
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
        'param-out', 'psalm-param-out', 'phpstan-param-out',
        'phpstan-param', 'phpstan-var', 'phpstan-return',
        'psalm-param', 'psalm-var', 'psalm-return',
    ];

    /**
     * @return non-empty-string
     */
    protected static function getVendorDirectory(): string
    {
        $classLoader = new \ReflectionClass(ClassLoader::class);

        return \dirname($classLoader->getFileName(), 2);
    }

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
            $this->getTypeStatementResult($type);
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
        // Known issues in phpdoc
        //
        if (true
            // Non-const expression in typedef (will not support)
            || \str_contains($expr, 'func_num_args() > ')
            // Conditional types with variables not supported
            // || \str_starts_with($e->getMessage(), 'Syntax error, unexpected "$')
            // Conditional types with template params not supported
            // || \str_starts_with($e->getMessage(), 'Syntax error, unexpected "is"')
        ) {
            $this->markTestIncomplete("Test is flagged as a known issue:\n" . $message);
        }

        $this->fail($message);
    }
}
