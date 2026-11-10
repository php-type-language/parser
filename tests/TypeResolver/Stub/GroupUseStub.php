<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\Group\First;
use Some\Group\Second as Alias;
use Some\Group\Third;

/**
 * @uses First
 * @uses Alias
 * @uses Third
 */
final class GroupUseStub {}
