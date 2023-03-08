<?php

declare(strict_types=1);

namespace Hyper\Type\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Usage
{
    /**
     * @param non-empty-string $sample
     */
    public function __construct(
        public readonly string $sample,
    ) {
    }
}


