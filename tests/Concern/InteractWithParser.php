<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PHPUnit\Framework\Assert;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Stmt\Statement;
use TypeLang\Parser\Parser;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use TypeLang\Parser\Traverser;

/**
 * @mixin TestCase
 */
trait InteractWithParser
{
    protected readonly Parser $parser;

    #[Before]
    public function setUpParser(): void
    {
        $this->parser = new Parser();
    }

    protected function parse(string $statement): ?Statement
    {
        return $this->parser->parse($statement);
    }

    protected function expectParseError(string $message = null): void
    {
        $this->expectException(ParseException::class);

        if ($message !== null) {
            $this->expectExceptionMessage($message);
        }
    }

    protected function parseToString(string $statement): ?string
    {
        $result = $this->parse($statement);

        if ($result === null) {
            return null;
        }

        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$result]);

        return $visitor->getOutput();
    }

    protected function assertStatementSame(string $statement, string $expected, string $message = ''): void
    {
        Assert::assertSame(
            \trim($expected),
            \trim($this->parseToString($statement)),
            $message,
        );
    }

    protected function assertStatementFails(string $statement, string $error): void
    {
        $this->expectParseError($error);

        $this->parse($statement);
    }
}
