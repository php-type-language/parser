<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Tests\TestCase;

#[Group('unit'), Group('type-lang/parser'), Group('ast')]
abstract class AstTestCase extends TestCase
{
    /**
     * @param class-string<\Throwable> $class
     */
    private function expectParserExceptionOf(string $class, ?string $message = null): void
    {
        $this->expectException($class);

        if ($message !== null) {
            $this->expectExceptionMessage($message);
        }
    }

    protected function expectParsingException(?string $message = null): void
    {
        $this->expectParserExceptionOf(\Throwable::class, $message);
    }

    protected function expectSemanticException(?string $message = null): void
    {
        $this->expectParserExceptionOf(SemanticException::class, $message);
    }

    protected function expectFeatureException(?string $message = null): void
    {
        $this->expectParserExceptionOf(FeatureNotEnabledException::class, $message);
    }
}
