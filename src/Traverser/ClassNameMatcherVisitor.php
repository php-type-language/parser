<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

class ClassNameMatcherVisitor extends MatcherVisitor
{
    /**
     * @param class-string<Node> $class
     * @param (\Closure(Node):bool)|null $break
     */
    public function __construct(string $class, ?\Closure $break = null)
    {
        $matcher = static fn (Node $node): bool => $node instanceof $class;

        parent::__construct($matcher, $break);
    }
}
