<?php

declare(strict_types=1);

use Some\Any;
use Some\Any\Thing as Alias;

/**
 * A class declared in the global namespace, without any `namespace`
 * declaration at all.
 *
 * @uses Any
 * @uses Alias
 */
final class GlobalScopeStub {}
