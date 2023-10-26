<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Type\TypeStatement;
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

    protected function getTypeStatementResult(string $statement): ?TypeStatement
    {
        return $this->parser->parseType($statement);
    }

    protected function getTypeStatementAsString(string $statement): ?string
    {
        $result = $this->getTypeStatementResult($statement);

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

    protected function assertTypeStatementCompilable(string $statement): void
    {
        $this->expectNotToPerformAssertions();

        $this->getTypeStatementResult($statement);
    }

    protected function assertTypeStatementSame(string $statement, string $expected, string $message = ''): void
    {
        $actual = \trim($this->getTypeStatementAsString($statement));

        Assert::assertSame(\trim($expected), $actual, $message);
    }

    protected function assertTypeStatementNotSame(string $statement, string $expected, string $message = ''): void
    {
        $actual = \trim($this->getTypeStatementAsString($statement));

        Assert::assertNotSame(\trim($expected), $actual, $message);
    }

    protected function assertTypeStatementFails(string $statement, string $error): void
    {
        $this->expectParseError($error);

        $this->getTypeStatementResult($statement);
    }
}
