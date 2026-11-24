<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\PropertyAccessor\PropertyAccessorInterface;
use TypeLang\Parser\Traverser\PropertyAccessor\SimplePropertyAccessor;
use TypeLang\Parser\Traverser\Visitor;
use TypeLang\Parser\Traverser\VisitorInterface;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;

final class TraverserTest extends TestCase
{
    /**
     * @var \ArrayObject<int<0, max>, non-empty-string>
     */
    private \ArrayObject $log;

    protected function setUp(): void
    {
        parent::setUp();

        $this->log = new \ArrayObject();
    }

    private function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode($this->parseName($name));
    }

    /**
     * @param non-empty-string $label
     * @return VisitorInterface&object{
     *     entered: list<class-string<Node>>,
     *     left: list<class-string<Node>>,
     *     before: int<0, max>,
     *     after: int<0, max>
     * }
     */
    private function recorder(string $label = 'a', ?Command $command = null): VisitorInterface
    {
        return new class ($this->log, $label, $command) extends Visitor {
            /**
             * @var list<class-string<Node>>
             */
            public array $entered = [];

            /**
             * @var list<class-string<Node>>
             */
            public array $left = [];

            /**
             * @var int<0, max>
             */
            public int $before = 0;

            /**
             * @var int<0, max>
             */
            public int $after = 0;

            /**
             * @param \ArrayObject<int<0, max>, non-empty-string> $log
             * @param non-empty-string $label
             */
            public function __construct(
                private readonly \ArrayObject $log,
                private readonly string $label,
                private readonly ?Command $command = null,
            ) {}

            public function before(): void
            {
                ++$this->before;
                $this->log[] = $this->label . ':before';
            }

            public function enter(Node $node): ?Command
            {
                $this->entered[] = $node::class;
                $this->log[] = $this->label . ':enter';

                return $this->command;
            }

            public function leave(Node $node): void
            {
                $this->left[] = $node::class;
                $this->log[] = $this->label . ':leave';
            }

            public function after(): void
            {
                ++$this->after;
                $this->log[] = $this->label . ':after';
            }
        };
    }

    #[Test]
    public function traverseVisitsEveryNodeInDepth(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([
            NamedTypeNode::class,
            Name::class,
        ], $visitor->entered);
    }

    #[Test]
    public function traverseLeavesEveryEnteredNode(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([
            Name::class,
            NamedTypeNode::class,
        ], $visitor->left);
    }

    #[Test]
    public function traverseCallsTheLifecycleHooksOnce(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([$this->type()]);

        self::assertSame(1, $visitor->before);
        self::assertSame(1, $visitor->after);
    }

    #[Test]
    public function beforeIsCalledPriorToAnyNodeAndAfterIsCalledLast(): void
    {
        Traverser::new([$this->recorder()])->traverse([$this->type()]);

        self::assertSame('a:before', $this->log[0]);
        self::assertSame('a:after', $this->log[\count($this->log) - 1]);
    }

    #[Test]
    public function skipChildrenCommandStopsTheDescent(): void
    {
        $visitor = $this->recorder(command: Command::SkipChildren);

        Traverser::new([$visitor])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([NamedTypeNode::class], $visitor->entered);
    }

    #[Test]
    public function skipChildrenCommandDoesNotSkipTheLeaveCall(): void
    {
        $visitor = $this->recorder(command: Command::SkipChildren);

        Traverser::new([$visitor])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([NamedTypeNode::class], $visitor->left);
    }

    #[Test]
    public function commandContainsTheSkipChildrenCase(): void
    {
        self::assertSame([Command::SkipChildren], Command::cases());
    }

    #[Test]
    public function throughReturnsThePassedVisitor(): void
    {
        $visitor = $this->recorder();

        $result = Traverser::through($visitor, [$this->type()]);

        self::assertSame($visitor, $result);
    }

    #[Test]
    public function throughTraversesTheNodes(): void
    {
        $visitor = Traverser::through($this->recorder(), [$this->type('Foo\\Bar')]);

        self::assertSame([
            NamedTypeNode::class,
            Name::class,
        ], $visitor->entered);
    }

    #[Test]
    public function newCreatesAnEmptyTraverser(): void
    {
        Traverser::new()->traverse([$this->type()]);

        self::assertSame([], $this->log->getArrayCopy());
    }

    #[Test]
    public function withReturnsANewInstance(): void
    {
        $traverser = Traverser::new();

        self::assertNotSame($traverser, $traverser->with($this->recorder()));
    }

    #[Test]
    public function withDoesNotModifyTheOriginalTraverser(): void
    {
        $traverser = Traverser::new();
        $traverser->with($this->recorder());

        $traverser->traverse([$this->type()]);

        self::assertSame([], $this->log->getArrayCopy());
    }

    #[Test]
    public function withAppendsTheVisitorToTheEnd(): void
    {
        Traverser::new([$this->recorder('a')])
            ->with($this->recorder('b'))
            ->traverse([]);

        self::assertSame([
            'a:before',
            'b:before',
            'a:after',
            'b:after',
        ], $this->log->getArrayCopy());
    }

    #[Test]
    public function withPrependsTheVisitorToTheBeginning(): void
    {
        Traverser::new([$this->recorder('a')])
            ->with($this->recorder('b'), true)
            ->traverse([]);

        self::assertSame([
            'b:before',
            'a:before',
            'b:after',
            'a:after',
        ], $this->log->getArrayCopy());
    }

    #[Test]
    public function withPropertyAccessorReturnsANewInstance(): void
    {
        $traverser = Traverser::new();

        self::assertNotSame(
            $traverser,
            $traverser->withPropertyAccessor(new SimplePropertyAccessor()),
        );
    }

    #[Test]
    public function withPropertyAccessorChangesTheTraversedChildren(): void
    {
        $accessor = new class implements PropertyAccessorInterface {
            public function unwrap(object $object): iterable
            {
                return [];
            }
        };

        $visitor = $this->recorder();

        Traverser::new([$visitor])
            ->withPropertyAccessor($accessor)
            ->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([NamedTypeNode::class], $visitor->entered);
    }

    #[Test]
    public function withPropertyAccessorDoesNotModifyTheOriginalTraverser(): void
    {
        $accessor = new class implements PropertyAccessorInterface {
            public function unwrap(object $object): iterable
            {
                return [];
            }
        };

        $visitor = $this->recorder();
        $traverser = Traverser::new([$visitor]);
        $traverser->withPropertyAccessor($accessor);

        $traverser->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([
            NamedTypeNode::class,
            Name::class,
        ], $visitor->entered);
    }

    #[Test]
    public function traverseVisitsEveryNodeOfTheList(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([$this->type('A'), $this->type('B')]);

        self::assertSame([
            NamedTypeNode::class,
            Name::class,
            NamedTypeNode::class,
            Name::class,
        ], $visitor->entered);
    }

    #[Test]
    public function traverseCanBeCalledSeveralTimes(): void
    {
        $visitor = $this->recorder();
        $traverser = Traverser::new([$visitor]);

        $traverser->traverse([$this->type()]);
        $traverser->traverse([$this->type()]);

        self::assertSame(2, $visitor->before);
        self::assertSame(2, $visitor->after);
    }

    #[Test]
    public function traverseVisitsTheNestedLogicalStatements(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([
            new UnionTypeNode([$this->type('A'), $this->type('B')]),
        ]);

        self::assertSame([
            UnionTypeNode::class,
            NamedTypeNode::class,
            Name::class,
            NamedTypeNode::class,
            Name::class,
        ], $visitor->entered);
    }

    #[Test]
    public function traverseOfAnEmptyListCallsOnlyTheLifecycleHooks(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse([]);

        self::assertSame([], $visitor->entered);
        self::assertSame(1, $visitor->before);
        self::assertSame(1, $visitor->after);
    }

    #[Test]
    public function traverseAcceptsAnyTraversableSetOfNodes(): void
    {
        $visitor = $this->recorder();

        Traverser::new([$visitor])->traverse(new \ArrayIterator([
            new Name('Example'),
        ]));

        self::assertSame([Name::class], $visitor->entered);
    }

    #[Test]
    public function traverserCanBeCreatedUsingTheConstructor(): void
    {
        $visitor = $this->recorder();

        (new Traverser([$visitor]))->traverse([$this->type()]);

        self::assertSame(1, $visitor->before);
    }

    #[Test]
    public function traverserConstructorAcceptsATraversableSetOfVisitors(): void
    {
        $visitor = $this->recorder();

        (new Traverser(new \ArrayIterator([$visitor])))->traverse([$this->type()]);

        self::assertSame(1, $visitor->before);
    }

    private function deepUnion(int $depth): UnionTypeNode
    {
        $node = new UnionTypeNode([$this->type('A'), $this->type('B')]);

        for ($i = 1; $i < $depth; ++$i) {
            $node = new UnionTypeNode([$this->type('A'), $node]);
        }

        return $node;
    }

    #[Test]
    public function everyVisitorEntersAndLeavesEveryNodeExactlyOnce(): void
    {
        $tree = $this->deepUnion(12);

        $baseline = $this->recorder();
        Traverser::new([$baseline])->traverse([$tree]);

        $visitors = [$this->recorder('a'), $this->recorder('b'), $this->recorder('c')];
        Traverser::new($visitors)->traverse([$tree]);

        foreach ($visitors as $visitor) {
            self::assertSame($baseline->entered, $visitor->entered);
            self::assertEqualsCanonicalizing($baseline->left, $visitor->left);
            self::assertCount(\count($baseline->entered), $visitor->left);
        }
    }

    #[Test]
    public function nodeIsEnteredByAllVisitorsThenChildrenThenLeftInReverseOrder(): void
    {
        Traverser::new([$this->recorder('a'), $this->recorder('b')])
            ->traverse([$this->type()]);

        self::assertSame([
            'a:before',
            'b:before',
            'a:enter',
            'b:enter',
            'a:enter',
            'b:enter',
            'b:leave',
            'a:leave',
            'b:leave',
            'a:leave',
            'a:after',
            'b:after',
        ], $this->log->getArrayCopy());
    }

    #[Test]
    public function skipChildrenOfOneVisitorDoesNotHideTheSubtreeFromAnother(): void
    {
        $skipping = $this->recorder('a', Command::SkipChildren);
        $other = $this->recorder('b');

        Traverser::new([$skipping, $other])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([NamedTypeNode::class], $skipping->entered);
        self::assertSame([NamedTypeNode::class], $skipping->left);
        self::assertSame([NamedTypeNode::class, Name::class], $other->entered);
        self::assertSame([Name::class, NamedTypeNode::class], $other->left);
    }

    #[Test]
    public function subtreeIsNotWalkedWhenEveryVisitorSkipsIt(): void
    {
        $first = $this->recorder('a', Command::SkipChildren);
        $second = $this->recorder('b', Command::SkipChildren);

        Traverser::new([$first, $second])->traverse([$this->type('Foo\\Bar')]);

        self::assertSame([NamedTypeNode::class], $first->entered);
        self::assertSame([NamedTypeNode::class], $second->entered);
    }

    #[Test]
    public function typeMapVisitorIsAppliedOnceWithAnotherVisitor(): void
    {
        $calls = 0;
        $mapper = new Traverser\TypeMapVisitor(static function (Name $name) use (&$calls): Name {
            ++$calls;

            return $name;
        });

        Traverser::new([$mapper, $this->recorder()])->traverse([
            new UnionTypeNode([$this->type('A'), $this->type('B')]),
        ]);

        self::assertSame(2, $calls);
    }
}
