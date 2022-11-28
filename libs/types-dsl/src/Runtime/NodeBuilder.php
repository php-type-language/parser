<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Runtime;

use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\ContextInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
final class NodeBuilder implements BuilderInterface
{
    /**
     * @param array<non-empty-string|positive-int, callable(ContextInterface, mixed):mixed> $reducers
     */
    public function __construct(
        private readonly array $reducers
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContextInterface $context, mixed $result): mixed
    {
        $state = $context->getState();

        if (isset($this->reducers[$state])) {
            return ($this->reducers[$state])($context, $result);
        }

        return $result;
    }
}
