<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Position\PositionInterface;

/**
 * @property-read TokenInterface $token
 * @property-read PositionInterface $position
 */
interface ParserRuntimeExceptionInterface extends
    ParserExceptionInterface {}
