<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

enum ParameterVariance
{
    case ANY;
    case IN;
    case OUT;
}
