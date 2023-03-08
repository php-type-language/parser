<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-covariant T of TypeInterface
 */
interface GenericTypeInterface extends TypeInterface
{
    /**
     * @return T
     */
    public function getType(): TypeInterface;
}
