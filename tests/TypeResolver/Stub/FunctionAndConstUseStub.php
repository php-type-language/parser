<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\ClassName;
use function Some\helperFunction;
use const Some\SOME_CONSTANT;
use Some\Aliased as Alias;

/**
 * @uses ClassName
 * @uses Alias
 */
final class FunctionAndConstUseStub {}
