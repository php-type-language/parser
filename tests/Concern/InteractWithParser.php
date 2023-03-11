<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Stmt\Statement;
use TypeLang\Parser\Parser;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

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

    protected function parse(string $expr): ?Statement
    {
        return $this->parser->parse($expr);
    }

    protected function expectParseError(string $message = null): void
    {
        $this->expectException(ParseException::class);

        if ($message !== null) {
            $this->expectExceptionMessage($message);
        }
    }
}
