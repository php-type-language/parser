<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Example\Some\Any\Test1 as Example1;
use Example\Some\Any1;
use Some\Any\Test2 as Example2;
use Some\Any2;

/**
 * @uses Example1
 * @uses Example2
 * @uses Any1
 * @uses Any2
 */
final class ClassWithGroupUsesStub {}
