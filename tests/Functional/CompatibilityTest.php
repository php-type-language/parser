<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional;

use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\FileInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Position\Position;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('functional')]
class CompatibilityTest extends TestCase
{
    private const TAGS = [
        'param', 'var', 'return',
        'phpstan-param', 'phpstan-var', 'phpstan-return',
        'psalm-param', 'psalm-var', 'psalm-return',
    ];

    public static function vendorDataProvider(): array
    {
        $result = [];
        $blocks = self::fetchDocTypesFromSources(
            sources: self::getSources(__DIR__ . '/../../vendor', ['php']),
            tags: self::TAGS,
        );

        foreach ($blocks as $phpdoc => [$file, $position]) {
            $result[$phpdoc] = [$phpdoc, self::location($file, $position)];
        }

        return $result;
    }

    public static function psalmDataProvider(): array
    {
        $result = [];
        $blocks = self::getDocTypesFromSources(
            sources: self::getSources(__DIR__ . '/psalm/5.15.0'),
            tags: self::TAGS,
        );

        foreach ($blocks as $phpdoc => [$file, $position]) {
            $result[$phpdoc] = [$phpdoc, self::location($file, $position)];
        }

        return $result;
    }

    public static function phpStanDataProvider(): array
    {
        $result = [];
        $blocks = self::getDocTypesFromSources(
            sources: self::getSources(__DIR__ . '/phpstan/1.10.35'),
            tags: self::TAGS,
        );

        foreach ($blocks as $phpdoc => [$file, $position]) {
            $result[$phpdoc] = [$phpdoc, self::location($file, $position)];
        }

        return $result;
    }

    protected static function location(ReadableInterface $src, PositionInterface $pos = null): string
    {
        $name = '<source#' . $src->getHash() . '>';

        if ($src instanceof FileInterface) {
            $name = \realpath($src->getPathname()) ?: $src->getPathname();
        }

        return $name . ':' . ($pos ?? Position::start())->getLine();
    }

    #[DataProvider('vendorDataProvider')]
    public function testVendorCode(string $type, string $location): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->parse($type);
        } catch (\Throwable $e) {
            $this->onFail($e, $type, $location);
        }
    }

    #[DataProvider('phpstanDataProvider')]
    public function testPhpStanStubs(string $type, string $location): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->parse($type);
        } catch (\Throwable $e) {
            $this->onFail($e, $type, $location);
        }
    }

    #[DataProvider('psalmDataProvider')]
    public function testPsalmStubs(string $type, string $location): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->parse($type);
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
            \str_contains($expr, 'ArrayObject[') ||
            \str_contains($expr, 'model\UserList[') ||
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
            // Ternary with "Type is X ? Y : Z" expression
            \str_starts_with($e->getMessage(), 'Syntax error, unexpected "is"') ||
            // Variables not supported
            \str_starts_with($e->getMessage(), 'Syntax error, unrecognized "$"')
        ) {
            $this->markTestIncomplete("Test is flagged as a known issue:\n" . $message);
        }

        $this->fail($message);
    }
}
