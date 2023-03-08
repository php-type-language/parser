<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-covariant T of TypeInterface
 * @template-implements GenericTypeInterface<T>
 */
abstract class Alias implements GenericTypeInterface
{
}
