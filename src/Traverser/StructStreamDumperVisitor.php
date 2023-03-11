<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

final class StructStreamDumperVisitor extends Visitor
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
        $class = \str_replace($this->prefix, '', $node::class);

        \fwrite($this->stream, $prefix . $class . "\n");

        return null;
    }

    public function leave(Node $node): void
    {
        /** @psalm-suppress InvalidPropertyAssignmentValue : enter() has been executed before */
        --$this->depth;
    }
}
