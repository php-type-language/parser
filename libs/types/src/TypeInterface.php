<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TPHPValue of mixed
 * @template TDatabaseValue of mixed
 *
 * @template-extends ParsableTypeInterface<TPHPValue, TDatabaseValue>
 * @template-extends SerializableTypeInterface<TDatabaseValue, TPHPValue>
 */
interface TypeInterface extends ParsableTypeInterface, SerializableTypeInterface
{
}
