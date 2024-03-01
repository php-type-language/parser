<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Traverser\VisitorInterface;

final class Traverser implements MutableTraverserInterface
{
    /**
     * @var list<VisitorInterface>
     */
    private array $visitors = [];

    /**
     * @param list<VisitorInterface> $visitors
     */
    public function __construct(iterable $visitors = [])
    {
        foreach ($visitors as $visitor) {
            $this->append($visitor);
        }
    }

    /**
     * @template TArgVisitor of VisitorInterface
     *
     * @param TArgVisitor $visitor
     * @param iterable<array-key, Node> $nodes
     *
     * @return TArgVisitor
     */
    public static function through(VisitorInterface $visitor, iterable $nodes): VisitorInterface
    {
        $instance = self::new([$visitor]);
        $instance->traverse($nodes);

        return $visitor;
    }

    /**
     * Creates a new traverser instance.
     *
     * @param list<VisitorInterface> $visitors
     */
    public static function new(iterable $visitors = []): self
    {
        return new self($visitors);
    }

    /**
     * @psalm-immutable
     */
    public function with(VisitorInterface $visitor, bool $prepend = false): self
    {
        $self = clone $this;

        return $prepend ? $self->prepend($visitor) : $self->append($visitor);
    }

    public function append(VisitorInterface $visitor): self
    {
        $this->visitors[] = $visitor;

        return $this;
    }

    public function prepend(VisitorInterface $visitor): self
    {
        \array_unshift($this->visitors, $visitor);

        return $this;
    }

    public function traverse(iterable $nodes): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->before();
        }

        $this->applyToIterable($nodes);

        foreach ($this->visitors as $visitor) {
            $visitor->after();
        }
    }

    private function getProperties(Node $node): iterable
    {
        return \get_object_vars($node);
    }

    private function applyToNode(Node $node): void
    {
        foreach ($this->visitors as $visitor) {
            $command = $visitor->enter($node);

            if ($command === null) {
                /** @psalm-suppress MixedAssignment */
                foreach ($this->getProperties($node) as $property) {
                    if ($property instanceof Node) {
                        $this->applyToNode($property);
                    } elseif (\is_iterable($property)) {
                        $this->applyToIterable($property);
                    }
                }
            }

            $visitor->leave($node);
        }
    }

    private function applyToIterable(iterable $nodes): void
    {
        foreach ($nodes as $node) {
            if (!$node instanceof Node) {
                break;
            }

            $this->applyToNode($node);
        }
    }
}
