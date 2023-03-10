<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Tests\Concern\CompileGrammarIfPossible;
use Hyper\Parser\Tests\Concern\InteractWithParser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('parser')]
abstract class TestCase extends BaseTestCase
{
    use CompileGrammarIfPossible;
    use InteractWithParser;
}
