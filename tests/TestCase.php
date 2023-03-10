<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use TypeLang\Parser\Tests\Concern\CompileGrammarIfPossible;
use TypeLang\Parser\Tests\Concern\InteractWithParser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('parser')]
abstract class TestCase extends BaseTestCase
{
    use CompileGrammarIfPossible;
    use InteractWithParser;
}
