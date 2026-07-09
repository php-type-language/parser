<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\Group\{
    First,
    Second as Alias,
    Third,
};

/**
 * @uses First
 * @uses Alias
 * @uses Third
 */
final class GroupUseStub {}
