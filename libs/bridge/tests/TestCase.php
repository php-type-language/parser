<?php

declare(strict_types=1);

namespace Hyper\Bridge\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('repository')]
abstract class TestCase extends BaseTestCase
{
}
