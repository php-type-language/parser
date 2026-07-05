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
        $this->visitors = \iterator_to_array($visitors, false);
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
     * Creates a new traverser instance.
     *
     * @param list<VisitorInterface> $visitors
     */
    public static function new(iterable $visitors = []): self
    {
        return new self($visitors);
    }

    /**
     * Gets a new {@see Traverser} with the specified property accessor.
     */
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

        $this->applyToIterable($nodes);

        foreach ($this->visitors as $visitor) {
            $visitor->after();
        }
    }

    private function applyToNode(Node $node): void
    {
        foreach ($this->visitors as $visitor) {
            $command = $visitor->enter($node);

            if ($command === null) {
                foreach ($this->propertyAccessor->unwrap($node) as $property) {
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

    /**
     * @param iterable<mixed, mixed> $nodes
     */
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
