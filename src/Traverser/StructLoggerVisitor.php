<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class StructLoggerVisitor extends Visitor
{
    private int $depth = 0;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $level = LogLevel::DEBUG,
    ) {}

    public function before(): void
    {
        $this->depth = 0;
    }

    public function enter(Node $node): ?Command
    {
        $this->logger->log($this->level, $node::class, [
            'depth' => ++$this->depth,
        ]);

        return null;
    }

    public function leave(Node $node): void
    {
        --$this->depth;
    }
}
