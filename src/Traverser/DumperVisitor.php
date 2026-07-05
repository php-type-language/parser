<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

abstract class DumperVisitor extends Visitor
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_SIMPLIFIED_NODE_NAMESPACE = 'TypeLang\\Type\\';

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
            $suffix .= \sprintf('(%s)', (string) $node);
        }

        $this->write($prefix . $suffix . "\n");

        return null;
    }

    public function leave(Node $node): void
    {
        // @phpstan-ignore-next-line : $depth is always non-negative
        --$this->depth;
    }
}
