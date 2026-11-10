<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Tests\TestCase;

/**
 * A base of the tests of the grammar: they tell whether a source is
 * accepted or not, and look at nothing else.
 */
#[Group('unit'), Group('type-lang/parser'), Group('syntax')]
abstract class SyntaxTestCase extends TestCase
{
    private static ?SyntaxChecker $checker = null;

    private static function checker(): SyntaxChecker
    {
        return self::$checker ??= new SyntaxChecker();
    }

    /**
     * @param int<0, max> $entry
     * @throws \JsonException
     */
    protected static function assertAccepted(string $source, int $entry = SyntaxChecker::Type): void
    {
        self::assertTrue(
            self::checker()->isValid($source, $entry),
            \sprintf('The source %s must be accepted', \json_encode($source, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR)),
        );
    }

    /**
     * @param int<0, max> $entry
     * @throws \JsonException
     */
    protected static function assertRejected(string $source, int $entry = SyntaxChecker::Type): void
    {
        self::assertFalse(
            self::checker()->isValid($source, $entry),
            \sprintf('The source %s must be rejected', \json_encode($source, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR)),
        );
    }
}
