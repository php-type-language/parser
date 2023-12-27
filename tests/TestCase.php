<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Tests\Concern\CompileGrammarIfPossible;
use TypeLang\Parser\Tests\Concern\InteractWithParser;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
    use CompileGrammarIfPossible;
    use InteractWithParser;
}
