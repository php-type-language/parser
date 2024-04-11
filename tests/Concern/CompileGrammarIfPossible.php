<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use Phplrt\Compiler\Compiler;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait CompileGrammarIfPossible
{
    #[BeforeClass]
    public static function setUpCompileGrammarIfPossible(): void
    {
        // Skip code assembly if the compiler is not available.
        if (!\class_exists(Compiler::class)) {
            return;
        }

        require_once __DIR__ . '/../../bin/build';
    }
}
