<?php

declare(strict_types=1);

namespace Hyper\Type;

class FalseLiteral extends BoolLiteral
{
    public function __construct()
    {
        parent::__construct(false);
    }
}
