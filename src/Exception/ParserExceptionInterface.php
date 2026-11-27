<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

/**
 * @property-read ReadableInterface $source An interface properties are
 *                available since PHP 8.4; for compatibility, they are defined
 *                using an annotation.
 */
interface ParserExceptionInterface extends \Throwable {}
