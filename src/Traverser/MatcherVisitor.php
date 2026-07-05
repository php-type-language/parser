<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

class MatcherVisitor extends Visitor
{
    public private(set) ?Node $node = null;

    public bool $isFound {
        get => $this->node !== null;
    }

    private bool $shouldContinue = false;

    /**
     * @param \Closure(Node):bool $matcher
     * @param (\Closure(Node):bool)|null $break
     */
    public function __construct(
        private readonly \Closure $matcher,
        private readonly ?\Closure $break = null,
    ) {}

    public function before(): void
    {
        $this->node = null;
    }

    public function enter(Node $node): ?Command
    {
        if ($this->node !== null || $this->shouldContinue) {
            return Command::SkipChildren;
        }

        if (($this->matcher)($node)) {
            $this->shouldContinue = true;
            $this->node = $node;

            return Command::SkipChildren;
        }

        if ($this->break !== null && ($this->break)($node)) {
            $this->shouldContinue = true;

            return Command::SkipChildren;
        }

        return null;
    }
}
