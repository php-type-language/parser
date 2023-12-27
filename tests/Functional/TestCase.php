<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Tests\TestCase as BaseTestCase;

#[Group('functional'), Group('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
}
