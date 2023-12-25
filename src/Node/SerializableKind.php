<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @psalm-require-implements SerializableInterface
 *
 * @mixin \BackedEnum
 * @mixin SerializableInterface
 */
trait SerializableKind
{
    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }

    /**
     * @return array{
     *     name: non-empty-string,
     *     value: int<0, max>|non-empty-string
     * }
     */
    public function toArray(): array
    {
        /**
         * @var array{
         *     name: non-empty-string,
         *     value: int<0, max>|non-empty-string
         * }
         */
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}
