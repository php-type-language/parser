<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Position\PositionInterface;

/**
 * @property-read ParsingErrorRange $range An interface properties are available
 *                since PHP 8.4; for compatibility, they are defined using an
 *                annotation.
 * @property-read PositionInterface $position An interface properties are
 *                available since PHP 8.4; for compatibility, they are defined
 *                using an annotation.
 */
interface ParserRuntimeExceptionInterface extends
    ParserExceptionInterface {}
