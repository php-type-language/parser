<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\A;
use Some\B as Bee;
use Some\C;

/**
 * @uses A
 * @uses Bee
 * @uses C
 */
final class MultipleImportsPerStatementStub {}
