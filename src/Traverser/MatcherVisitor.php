<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

class MatcherVisitor extends Visitor
{
    private ?Node $found = null;

    private bool $shouldContinue = false;

    /**
     * @param \Closure(Node):bool $matcher
     * @param (\Closure(Node):bool)|null $break
     */
    public function __construct(
        private readonly \Closure $matcher,
        private readonly ?\Closure $break = null,
    ) {}

    public function isFound(): bool
    {
        return $this->found !== null;
    }

    public function getFoundNode(): ?Node
    {
        return $this->found;
    }

    public function before(): void
    {
        $this->found = null;
    }

    public function enter(Node $node): ?Command
    {
        if ($this->found !== null || $this->shouldContinue) {
            return Command::SKIP_CHILDREN;
        }

        if (($this->matcher)($node)) {
            $this->shouldContinue = true;
            $this->found = $node;

            return Command::SKIP_CHILDREN;
        }

        if ($this->break !== null && ($this->break)($node)) {
            $this->shouldContinue = true;

            return Command::SKIP_CHILDREN;
        }

        return null;
    }
}
