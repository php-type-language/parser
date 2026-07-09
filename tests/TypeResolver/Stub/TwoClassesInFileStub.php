<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\Shared;
use Some\Other as Alias;

final class FirstDeclaredClass {}

/**
 * @uses Shared
 * @uses Alias
 */
final class TwoClassesInFileStub {}
