<?php

declare(strict_types=1);

namespace Hyper\Parser;

use Hyper\Parser\Node\Node;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\ContextInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
final class Builder implements BuilderInterface
{
    /**
     * @param array<non-empty-string|positive-int, callable(ContextInterface, mixed):mixed> $reducers
     */
    public function __construct(
        private readonly array $reducers,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContextInterface $context, mixed $result): mixed
    {
        $state = $context->getState();

        if (isset($this->reducers[$state])) {
            $result = ($this->reducers[$state])($context, $result);

            if ($result instanceof Node) {
                $this->process($result, $context);
            }
        }

        return $result;
    }

    /**
     * @param Node $node
     * @param ContextInterface $ctx
     *
     * @return void
     */
    private function process(Node $node, ContextInterface $ctx): void
    {
        $token = $ctx->getToken();

        $node->offset = $token->getOffset();
    }
}
