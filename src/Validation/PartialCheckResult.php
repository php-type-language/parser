<?php

declare(strict_types=1);

namespace TypeLang\Parser\Validation;

/**
 * A source the grammar has read only the beginning of, the rest of which
 * begins at the {@see $offset}.
 */
final class PartialCheckResult extends FailureCheckResult {}
