<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\Method\Any;
use Some\Method\Any\Test as Example;

/**
 * @uses Any
 * @uses Example
 */
final class ClassWithMethodStub
{
    public function example(): void {}
}
