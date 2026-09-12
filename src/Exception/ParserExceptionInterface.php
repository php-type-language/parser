<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

/**
 * @property-read ReadableInterface $source
 */
interface ParserExceptionInterface extends \Throwable {}
