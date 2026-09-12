<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

abstract class DumperVisitor extends Visitor
{
    /**
     * @var non-empty-string
     */
    public const DEFAULT_SIMPLIFIED_NODE_NAMESPACE = 'TypeLang\\Type\\';

    /**
     * @var int<0, max>
     */
    private int $depth = 0;

    public function __construct(
        private readonly string $simplifyNodeNamespace = self::DEFAULT_SIMPLIFIED_NODE_NAMESPACE,
    ) {}

    abstract protected function write(string $data): void;

    public function before(): void
    {
        $this->depth = 0;
    }

    public function enter(Node $node): ?Command
    {
        $prefix = \str_repeat('  ', $this->depth++);
        $suffix = \str_replace($this->simplifyNodeNamespace, '', $node::class);

        if ($node instanceof \Stringable) {
            $suffix .= $this->printStringableNodeSuffix($node);
        } else {
            $suffix .= $this->printNodePropertiesSuffix($node);
        }

        $this->write($prefix . $suffix . "\n");

        return null;
    }

    /**
     * @return non-empty-string
     */
    private function printStringableNodeSuffix(\Stringable $node): string
    {
        return \sprintf('(%s)', (string) $node);
    }

    /**
     * Returns a "(prop=value, ...)" suffix built from the writable scalar
     * properties of the node, or an empty string in case of there are none.
     */
    private function printNodePropertiesSuffix(Node $node): string
    {
        $result = [];

        $reflection = new \ReflectionObject($node);

        foreach ($reflection->getProperties() as $property) {
            // Skip readonly + static and builtin "offset" properties
            if ($property->isStatic() || $property->isReadOnly() || $property->getName() === 'offset') {
                continue;
            }

            $value = $property->getValue($node);

            // Skip non-scalar properties
            if (!\is_scalar($value)) {
                continue;
            }

            $result[] = \sprintf('%s=%s', $property->getName(), \var_export($value, true));
        }

        if ($result === []) {
            return '';
        }

        return \sprintf('(%s)', \implode(', ', $result));
    }

    public function leave(Node $node): void
    {
        // @phpstan-ignore-next-line : $depth is always non-negative
        --$this->depth;
    }
}
