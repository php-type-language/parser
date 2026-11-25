<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\PropertyAccessor\PropertyAccessorInterface;
use TypeLang\Parser\Traverser\PropertyAccessor\SimplePropertyAccessor;
use TypeLang\Parser\Traverser\VisitorInterface;
use TypeLang\Type\Node;

final class Traverser implements TraverserInterface
{
    /**
     * @var list<VisitorInterface>
     */
    private array $visitors = [];

    /**
     * @param iterable<mixed, VisitorInterface> $visitors
     */
    public function __construct(
        iterable $visitors = [],
        private PropertyAccessorInterface $propertyAccessor = new SimplePropertyAccessor(),
    ) {
        $this->visitors = \is_array($visitors)
            ? \array_values($visitors)
            : \iterator_to_array($visitors, false);
    }

    /**
     * @template TArgVisitor of VisitorInterface
     * @param TArgVisitor $visitor
     * @param iterable<array-key, Node> $nodes
     * @return TArgVisitor
     */
    public static function through(VisitorInterface $visitor, iterable $nodes): VisitorInterface
    {
        $instance = self::new([$visitor]);
        $instance->traverse($nodes);

        return $visitor;
    }

    /**
     * @param list<VisitorInterface> $visitors
     */
    public static function new(iterable $visitors = []): self
    {
        return new self($visitors);
    }

    public function withPropertyAccessor(PropertyAccessorInterface $propertyAccessor): self
    {
        $self = clone $this;
        $self->propertyAccessor = $propertyAccessor;

        return $self;
    }

    public function with(VisitorInterface $visitor, bool $prepend = false): self
    {
        $self = clone $this;

        if ($prepend) {
            \array_unshift($self->visitors, $visitor);
        } else {
            $self->visitors[] = $visitor;
        }

        return $self;
    }

    public function traverse(iterable $nodes): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->before();
        }

        $this->applyToIterable($nodes, $this->visitors);

        foreach ($this->visitors as $visitor) {
            $visitor->after();
        }
    }

    /**
     * Enters the node with every active visitor, descends once with the ones
     * that have not skipped the children, then leaves the node with every
     * visitor that has entered it, in the reverse order.
     *
     * @param list<VisitorInterface> $visitors
     */
    private function applyToNode(Node $node, array $visitors): void
    {
        // The list is copied only when some visitor skips the children
        $descending = $visitors;
        $skipped = false;

        foreach ($visitors as $index => $visitor) {
            if ($visitor->enter($node) === null) {
                if ($skipped) {
                    $descending[] = $visitor;
                }

                continue;
            }

            if (!$skipped) {
                $skipped = true;
                $descending = \array_slice($visitors, 0, $index);
            }
        }

        if ($descending !== []) {
            foreach ($this->propertyAccessor->unwrap($node) as $property) {
                if ($property instanceof Node) {
                    $this->applyToNode($property, $descending);
                } elseif (\is_iterable($property)) {
                    $this->applyToIterable($property, $descending);
                }
            }
        }

        for ($index = \count($visitors) - 1; $index >= 0; --$index) {
            $visitors[$index]->leave($node);
        }
    }

    /**
     * @param iterable<mixed, mixed> $nodes
     * @param list<VisitorInterface> $visitors
     */
    private function applyToIterable(iterable $nodes, array $visitors): void
    {
        foreach ($nodes as $node) {
            if (!$node instanceof Node) {
                break;
            }

            $this->applyToNode($node, $visitors);
        }
    }
}
