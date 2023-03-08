<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-covariant T of TypeInterface
 *
 * @template-extends GenericTypeInterface<T>
 */
interface AliasInterface extends GenericTypeInterface
{
}
