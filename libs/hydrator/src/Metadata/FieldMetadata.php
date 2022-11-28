<?php

declare(strict_types=1);

namespace Hyper\Hydrator\Metadata;

use Hyper\Type\ParsableTypeInterface;
use Hyper\Type\SerializableTypeInterface;

class FieldMetadata
{
    /**
     * @param non-empty-string $name
     * @param SerializableTypeInterface $serializer
     * @param ParsableTypeInterface $parser
     */
    public function __construct(
        public readonly string $name,
        public readonly SerializableTypeInterface $serializer,
        public readonly ParsableTypeInterface $parser,
    ) {
    }
}
