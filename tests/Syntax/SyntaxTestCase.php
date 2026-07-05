<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Exception\FeatureNotAllowedException;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Tests\TestCase;

#[Group('unit'), Group('type-lang/parser')]
abstract class SyntaxTestCase extends TestCase
{
    /**
     * @param class-string<ParserExceptionInterface> $class
     */
    private function expectParserExceptionOf(string $class, ?string $message = null): void
    {
        $this->expectException($class);

        if ($message !== null) {
            $this->expectExceptionMessageIsOrContains($message);
        }
    }

    protected function expectParsingException(?string $message = null): void
    {
        $this->expectParserExceptionOf(ParseException::class, $message);
    }

    protected function expectSemanticException(?string $message = null): void
    {
        $this->expectParserExceptionOf(SemanticException::class, $message);
    }

    protected function expectFeatureException(?string $message = null): void
    {
        $this->expectParserExceptionOf(FeatureNotAllowedException::class, $message);
    }
}
