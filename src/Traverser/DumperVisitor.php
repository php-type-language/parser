<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

final class DumperVisitor extends Visitor
{
    /**
     * @var int<0, max>
     */
    private int $depth = 0;

    private readonly string $prefix;

    /**
     * @var resource
     */
    private readonly mixed $stream;

    public function __construct(
        bool $simplifyNames = true,
        string $stream = 'php://stderr',
    ) {
        $this->prefix = $simplifyNames ? 'TypeLang\\Parser\\Node\\' : '';
        $this->stream = \fopen($stream, 'ab+');
    }

    public function before(): void
    {
        $this->depth = 0;
    }

    public function enter(Node $node): ?Command
    {
        $prefix = \str_repeat('  ', ++$this->depth);
        $suffix = \str_replace($this->prefix, '', $node::class);

        if ($node instanceof \Stringable) {
            $suffix .= \sprintf('(%s)', (string)$node);
        }

        \fwrite($this->stream, $prefix . $suffix . "\n");

        return null;
    }

    public function leave(Node $node): void
    {
        /** @psalm-suppress InvalidPropertyAssignmentValue : enter() has been executed before */
        --$this->depth;
    }
}
