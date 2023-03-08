<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Exception\ParseException;
use Hyper\Parser\Node\Stmt\NamedTypeStmt;
use Hyper\Parser\Node\Stmt\Statement;
use Hyper\Parser\Parser;
use Phplrt\Compiler\Compiler;
use Phplrt\Contracts\Parser\ParserInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('parser')]
abstract class TestCase extends BaseTestCase
{
    private readonly ParserInterface $parser;

    public static function setUpBeforeClass(): void
    {
        if (\class_exists(Compiler::class)) {
            require_once __DIR__ . '/../bin/build';
        }

        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new Parser();
    }

    protected function parse(string $expr): ?Statement
    {
        foreach ($this->parser->parse($expr) as $node) {
            return $node;
        }

        return null;
    }

    protected function expectParseError(string $template = null): void
    {
        $this->expectException(ParseException::class);

        if ($template !== null) {
            $this->expectExceptionMessage($template);
        }
    }
}
