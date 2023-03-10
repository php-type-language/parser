<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests\Unit;

use Hyper\Parser\Tests\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser'), Group('unit')]
abstract class TestCase extends BaseTestCase
{
}
