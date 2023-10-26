<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Definition\Template;

enum ParameterVariance
{
    case ANY;
    case IN;
    case OUT;
}
