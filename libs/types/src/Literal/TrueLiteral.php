<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

class TrueLiteral extends BoolLiteral
{
    public function __construct()
    {
        parent::__construct(true);
    }
}
