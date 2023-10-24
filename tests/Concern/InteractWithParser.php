<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Stmt\Type\TypeStatement;
use TypeLang\Parser\Parser;
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

    protected function getStatementResult(string $statement): ?TypeStatement
    {
        return $this->parser->parse($statement);
    }

    protected function getStatementAsString(string $statement): ?string
    {
        $result = $this->getStatementResult($statement);

        if ($result === null) {
            return null;
        }

        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$result]);

        return \trim($visitor->getOutput());
    }

    protected function expectParseError(string $message = null): void
    {
        $this->expectException(ParseException::class);

        if ($message !== null) {
            $this->expectExceptionMessage($message);
        }
    }

    protected function assertStatementCompilable(string $statement): void
    {
        $this->expectNotToPerformAssertions();

        $this->getStatementResult($statement);
    }

    protected function assertStatementSame(string $statement, string $expected, string $message = ''): void
    {
        $actual = \trim($this->getStatementAsString($statement));

        Assert::assertSame(\trim($expected), $actual, $message);
    }

    protected function assertStatementNotSame(string $statement, string $expected, string $message = ''): void
    {
        $actual = \trim($this->getStatementAsString($statement));

        Assert::assertNotSame(\trim($expected), $actual, $message);
    }

    protected function assertStatementFails(string $statement, string $error): void
    {
        $this->expectParseError($error);

        $this->getStatementResult($statement);
    }
}
