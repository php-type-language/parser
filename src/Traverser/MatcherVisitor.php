<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

class MatcherVisitor extends Visitor
{
    public ?Node $node = null;

    private bool $shouldContinue = false;

    public function __construct(
        /**
         * @var \Closure(Node):bool
         */
        private readonly \Closure $matcher,
        /**
         * @var (\Closure(Node):bool)|null
         */
        private readonly ?\Closure $break = null,
    ) {}

    /**
     * Returns {@see true} in case of a node matching the criteria was found.
     */
    public function hasMatches(): bool
    {
        return $this->node !== null;
    }

    public function before(): void
    {
        $this->node = null;
        $this->shouldContinue = false;
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
