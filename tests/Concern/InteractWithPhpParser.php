<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use PhpParser\Parser as PhpParserInterface;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 * @psalm-require-extends TestCase
 */
trait InteractWithPhpParser
{
    private static ?PhpParserInterface $php = null;

    /**
     * @return array<array-key, Stmt>
     * @throws ExpectationFailedException
     * @throws \LogicException
     * @api
     */
    public static function getStatementsFromPhpCode(string $code): array
    {
        $parser = self::getPhpParser();

        $result = $parser->parse($code);

        self::assertNotNull($result, 'Failed to parse PHP code');

        return $result;
    }

    /**
     * @return array<array-key, Stmt>|null
     * @throws ExpectationFailedException
     * @throws \LogicException
     * @throws AssertionFailedError
     * @api
     */
    public static function getStatementsFromPhpFile(string $pathname): array
    {
        $parser = self::getPhpParser();

        try {
            $result = $parser->parse(\file_get_contents($pathname));
        } catch (\Throwable $e) {
            self::fail('Failed to parse PHP file "'. $pathname. '": ' . $e->getMessage());
        }

        self::assertNotNull($result, 'Failed to parse PHP file "' . $pathname . '"');

        return $result;
    }

    /**
     * @return PhpParserInterface
     * @throws \LogicException
     */
    private static function getPhpParser(): PhpParserInterface
    {
        if (self::$php !== null) {
            return self::$php;
        }

        $factory = new ParserFactory();

        if (\method_exists($factory, 'createForHostVersion')) {
            return self::$php = $factory->createForHostVersion();
        }

        return self::$php = $factory->create(ParserFactory::PREFER_PHP7);
    }
}
