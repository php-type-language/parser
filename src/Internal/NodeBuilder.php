<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\Context;
use TypeLang\Type\Node;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 */
final readonly class NodeBuilder implements BuilderInterface
{
    /**
     * @param array<int<0, max>|non-empty-string, callable(Context, mixed):mixed> $reducers
     */
    public function __construct(
        private array $reducers,
    ) {}

    public function build(Context $context, mixed $result): mixed
    {
        if (!isset($this->reducers[$context->state])) {
            return $result;
        }

        $result = ($this->reducers[$context->state])($context, $result);

        if ($result instanceof Node && $result->offset === 0) {
            $result->offset = $context->lastProcessedToken->getOffset();
        }

        return $result;
    }
}
