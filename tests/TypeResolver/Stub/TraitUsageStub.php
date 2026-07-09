<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\ImportedClass;

trait UsageStubHelperTrait {}

/**
 * The `use UsageStubHelperTrait;` inside the body is a trait usage, not an
 * import, and must not appear among the class imports.
 *
 * @uses ImportedClass
 */
final class TraitUsageStub
{
    use UsageStubHelperTrait;
}
