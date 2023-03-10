<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests\Concern;

use Hyper\Parser\Exception\ParseException;
use Hyper\Parser\Node\Stmt\Statement;
use Hyper\Parser\Parser;
use Phplrt\Contracts\Parser\ParserInterface;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait InteractWithParser
{
    protected readonly ParserInterface $parser;

    #[Before]
    public function setUpParser(): void
    {
        $this->parser = new Parser();
    }

    protected function parse(string $expr): ?Statement
    {
        foreach ($this->parser->parse($expr) as $node) {
            return $node;
        }

        return null;
    }

    protected function expectParseError(string $message = null): void
    {
        $this->expectException(ParseException::class);

        if ($message !== null) {
            $this->expectExceptionMessage($message);
        }
    }
}
