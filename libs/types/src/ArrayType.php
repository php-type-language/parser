<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Attribute\Usage;
use Hyper\Type\Complex\Struct;

/**
 * @template-covariant TKey of TypeInterface
 * @template-covariant TValue of TypeInterface
 */
#[Usage('{field: Type}'), Usage('{Type}')]
#[Usage('<ValueType>'), Usage('<KeyType, ValueType>')]
class ArrayType extends Type implements ComplexTypeInterface, GenericTypeInterface
{
    /**
     * The type of the array key.
     *
     * @var TKey
     */
    public readonly TypeInterface $key;

    /**
     * The type of the array value.
     *
     * @var TValue
     */
    public readonly TypeInterface $value;

    /**
     * Exact description of the array structure.
     */
    public readonly Struct $struct;

    /**
     * @param TKey|Struct $key
     * @param TValue|null $value
     */
    public function __construct(
        TypeInterface|Struct $key = null,
        TypeInterface $value = null,
    ) {
        switch (true) {
            // All arguments are empty
            case $key === null:
                $this->struct = Struct::any();
                $this->key = $this->struct->getKeyType();
                $this->value = MixedType::getInstance();
                break;

            // First argument is an array shape
            case $key instanceof Struct:
                $this->struct = $key;
                $this->key = $this->struct->getKeyType();
                $this->value = MixedType::getInstance();
                break;

            // First argument is a value type
            case $value === null:
                $this->struct = Struct::any();
                $this->key = $this->struct->getKeyType();
                $this->value = $key;
                break;

            default:
                $this->key = $key;
                $this->value = $value;
                $this->struct = Struct::any();
        }
    }

    /**
     * @return TKey
     */
    public function getKeyType(): TypeInterface
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): TypeInterface
    {
        return $this->value;
    }
}
