<?php

declare(strict_types=1);

namespace Hyper\Hydrator\Metadata;

use Ds\Map;

abstract class ClassLikeMetadata
{
    /**
     * @var Map<non-empty-string, FieldMetadata>
     */
    public readonly Map $fields;

    /**
     * @param class-string $class
     */
    public function __construct(
        public readonly string $class,
    ) {
        $this->fields = new Map();
    }

    /**
     * @param non-empty-string $name
     * @param FieldMetadata $field
     *
     * @return $this
     */
    public function addField(string $name, FieldMetadata $field): self
    {
        $this->fields->offsetSet($name, $field);

        return $this;
    }
}
