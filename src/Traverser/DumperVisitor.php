<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

abstract class DumperVisitor extends Visitor
{
    /**
     * @var non-empty-string
     */
    private const NODE_NAMESPACE_PREFIX = 'TypeLang\\Parser\\Node\\';

    /**
     * @var int<0, max>
     */
    private int $depth = 0;

    private readonly string $prefix;

    public function __construct(bool $simplifyNames = true)
    {
        $this->prefix = $simplifyNames ? self::NODE_NAMESPACE_PREFIX : '';
    }

    abstract protected function write(string $data): void;

    public function before(): void
    {
        $this->depth = 0;
    }

    public function enter(Node $node): ?Command
    {
        $prefix = \str_repeat('  ', $this->depth++);
        $suffix = \str_replace($this->prefix, '', $node::class);

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
