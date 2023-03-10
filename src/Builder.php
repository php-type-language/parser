<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Node;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\ContextInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
final class Builder implements BuilderInterface
{
    /**
     * @param array<int<0, max>|non-empty-string, callable(ContextInterface, mixed):mixed> $reducers
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

            if ($result instanceof Node && $result->offset === 0) {
                $token = $context->getToken();
                $result->offset = $token->getOffset();
            }
        }

        return $result;
    }
}
