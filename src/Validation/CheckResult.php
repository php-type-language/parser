<?php

declare(strict_types=1);

namespace TypeLang\Parser\Validation;

/**
 * The outcome of checking whether a source is a type the grammar describes.
 *
 * @phpstan-sealed FailureCheckResult|PartialCheckResult|SuccessfulCheckResult
 */
abstract class CheckResult {}
