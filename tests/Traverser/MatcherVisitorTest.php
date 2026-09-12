<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\MatcherVisitor;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\UnionTypeNode;

final class MatcherVisitorTest extends TestCase
{
    private function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    #[Test]
    public function nothingIsFoundBeforeTheTraversal(): void
    {
        $visitor = new MatcherVisitor(static fn(Node $node): bool => true);

        self::assertFalse($visitor->hasMatches());
        self::assertNull($visitor->node);
    }

    #[Test]
    public function theMatchedNodeIsAvailableAfterTheTraversal(): void
    {
        $expected = $this->type();

        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool => $node instanceof NamedTypeNode),
            [$expected],
        );

        self::assertTrue($visitor->hasMatches());
        self::assertSame($expected, $visitor->node);
    }

    #[Test]
    public function nothingIsFoundInCaseOfNoNodeMatches(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool => $node instanceof UnionTypeNode),
            [$this->type()],
        );

        self::assertFalse($visitor->hasMatches());
        self::assertNull($visitor->node);
    }

    #[Test]
    public function theFirstMatchingNodeIsReturned(): void
    {
        $first = $this->type('First');
        $second = $this->type('Second');

        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool => $node instanceof NamedTypeNode),
            [$first, $second],
        );

        self::assertSame($first, $visitor->node);
    }

    #[Test]
    public function nestedNodesAreMatched(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool => $node instanceof Name),
            [$this->type('Foo\\Bar')],
        );

        self::assertInstanceOf(Name::class, $visitor->node);
        self::assertSame('Foo\\Bar', $visitor->node->toString());
    }

    #[Test]
    public function theBreakConditionStopsTheSearch(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(
                matcher: static fn(Node $node): bool => $node instanceof Name,
                break: static fn(Node $node): bool => $node instanceof NamedTypeNode,
            ),
            [$this->type('Foo\\Bar')],
        );

        self::assertFalse($visitor->hasMatches());
    }

    #[Test]
    public function theMatcherHasAPriorityOverTheBreakCondition(): void
    {
        $expected = $this->type();

        $visitor = Traverser::through(
            new MatcherVisitor(
                matcher: static fn(Node $node): bool => $node instanceof NamedTypeNode,
                break: static fn(Node $node): bool => $node instanceof NamedTypeNode,
            ),
            [$expected],
        );

        self::assertSame($expected, $visitor->node);
    }

    #[Test]
    public function theBreakConditionStopsTheWholeTraversal(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(
                matcher: static fn(Node $node): bool => $node instanceof Identifier,
                break: static fn(Node $node): bool => $node instanceof UnionTypeNode,
            ),
            [
                new UnionTypeNode([$this->type('A'), $this->type('B')]),
                new Identifier('Example'),
            ],
        );

        self::assertFalse($visitor->hasMatches());
    }

    #[Test]
    public function enterSkipsTheChildrenOfTheMatchedNode(): void
    {
        $visitor = new MatcherVisitor(static fn(Node $node): bool => true);

        self::assertSame(Command::SkipChildren, $visitor->enter($this->type()));
    }

    #[Test]
    public function enterDescendsIntoTheNonMatchingNode(): void
    {
        $visitor = new MatcherVisitor(static fn(Node $node): bool => false);

        self::assertNull($visitor->enter($this->type()));
    }

    #[Test]
    public function beforeResetsThePreviouslyMatchedNode(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool => true),
            [$this->type()],
        );

        $visitor->before();

        self::assertFalse($visitor->hasMatches());
        self::assertNull($visitor->node);
    }

    #[Test]
    public function theSameVisitorCanBeUsedSeveralTimes(): void
    {
        $visitor = new MatcherVisitor(static fn(Node $node): bool => $node instanceof NamedTypeNode);
        $traverser = Traverser::new([$visitor]);

        $traverser->traverse([$this->type('First')]);
        $expected = $this->type('Second');
        $traverser->traverse([$expected]);

        self::assertTrue($visitor->hasMatches());
        self::assertSame($expected, $visitor->node);
    }

    #[Test]
    public function theBreakConditionDoesNotAffectTheNextTraversal(): void
    {
        $visitor = new MatcherVisitor(
            matcher: static fn(Node $node): bool => $node instanceof Name,
            break: static fn(Node $node): bool => $node instanceof UnionTypeNode,
        );

        $traverser = Traverser::new([$visitor]);

        $traverser->traverse([new UnionTypeNode([$this->type('A'), $this->type('B')])]);
        $traverser->traverse([$this->type('Foo\\Bar')]);

        self::assertTrue($visitor->hasMatches());
    }

    #[Test]
    public function theMatcherReceivesEveryVisitedNode(): void
    {
        $visited = [];

        Traverser::through(
            new MatcherVisitor(static function (Node $node) use (&$visited): bool {
                $visited[] = $node::class;

                return false;
            }),
            [$this->type('Foo\\Bar')],
        );

        self::assertSame([
            NamedTypeNode::class,
            Name::class,
        ], $visited);
    }

    #[Test]
    public function theMatcherCanBeAnArbitraryCondition(): void
    {
        $visitor = Traverser::through(
            new MatcherVisitor(static fn(Node $node): bool
                => $node instanceof NamedTypeNode && $node->name->toString() === 'Second'),
            [$this->type('First'), $this->type('Second')],
        );

        self::assertInstanceOf(NamedTypeNode::class, $visitor->node);
        self::assertSame('Second', $visitor->node->name->toString());
    }
}
