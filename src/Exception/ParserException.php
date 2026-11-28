<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

abstract class ParserException extends \LogicException implements
    ParserExceptionInterface {}
