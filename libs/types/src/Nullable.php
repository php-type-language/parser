<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Variadic\UnionType;

/**
 * @template-covariant T of TypeInterface
 * @template-extends Alias<UnionType<NullLiteral, T>>
 */
class Nullable extends Alias
{
    private readonly UnionType $alias;

    public function __construct(
        public readonly TypeInterface $type,
    ) {
        $this->alias = new UnionType(new NullLiteral(), $this->type);
    }

    public function getType(): TypeInterface
    {
        return $this->alias;
    }
}
