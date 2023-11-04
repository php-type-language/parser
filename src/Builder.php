<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Parser\Context;
use TypeLang\Parser\Node\Node;
use Phplrt\Parser\BuilderInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
final class Builder implements BuilderInterface
{
    /**
     * @param array<int<0, max>|non-empty-string, callable(Context, mixed):mixed> $reducers
     */
    public function __construct(
        private readonly array $reducers,
    ) {}

    public function build(Context $context, mixed $result): mixed
    {
        $state = $context->getState();

        if (isset($this->reducers[$state])) {
            /** @psalm-suppress MixedAssignment */
            $result = ($this->reducers[$state])($context, $result);

            if ($context instanceof Context
                && $result instanceof Node
                && $result->offset === 0
            ) {
                $processed = $context->lastProcessedToken;
                $ordinal = $context->lastOrdinalToken;

                $result->offset = $processed->getOffset();
                $result->offsetTo = $ordinal?->getOffset() ?? $result->offset;
            }
        }

        return $result;
    }
}
