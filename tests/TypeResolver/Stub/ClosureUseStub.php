<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\RealImport;

$closureUseStubCaptured = 42;

$closureUseStubFactory = static function () use ($closureUseStubCaptured): int {
    return $closureUseStubCaptured;
};

/**
 * The namespace-level closure above carries a `use (...)` clause that captures
 * variables — it is not an import and must be ignored.
 *
 * @uses RealImport
 */
final class ClosureUseStub {}
