<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\Other as Alias;
use Some\Shared;

final class FirstDeclaredClass {}

/**
 * @uses Shared
 * @uses Alias
 */
final class TwoClassesInFileStub {}
