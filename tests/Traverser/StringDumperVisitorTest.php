<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\DumperVisitor;
use TypeLang\Parser\Traverser\StringDumperVisitor;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\UnionTypeNode;

final class StringDumperVisitorTest extends TestCase
{
    /**
     * @param iterable<array-key, Node> $nodes
     */
    private function dump(iterable $nodes, ?string $namespace = null): string
    {
        $visitor = $namespace === null
            ? new StringDumperVisitor()
            : new StringDumperVisitor($namespace);

        Traverser::new([$visitor])->traverse($nodes);

        return $visitor->output;
    }

    #[Test]
    public function theOutputIsEmptyBeforeTheTraversal(): void
    {
        self::assertSame('', (new StringDumperVisitor())->output);
    }

    #[Test]
    public function theOutputIsEmptyInCaseOfNoNodes(): void
    {
        self::assertSame('', $this->dump([]));
    }

    #[Test]
    public function theNodeNameIsPrintedWithoutTheSimplifiedNamespace(): void
    {
        self::assertSame(
            "NamedTypeNode\n  Name(Foo\\Bar)\n",
            $this->dump([new NamedTypeNode(Name::createFromString('Foo\\Bar'))]),
        );
    }

    #[Test]
    public function theSimplifiedNamespaceCanBeChanged(): void
    {
        self::assertSame(
            "Type\\Identifier(Example)\n",
            $this->dump([new Identifier('Example')], 'TypeLang\\'),
        );
    }

    #[Test]
    public function theNodeNameIsPrintedAsIsInCaseOfANonMatchingNamespace(): void
    {
        self::assertSame(
            "TypeLang\\Type\\Identifier(Example)\n",
            $this->dump([new Identifier('Example')], 'Example\\'),
        );
    }

    #[Test]
    public function theDefaultSimplifiedNamespaceIsUsed(): void
    {
        self::assertSame(
            $this->dump([new Identifier('Example')]),
            $this->dump([new Identifier('Example')], DumperVisitor::DEFAULT_SIMPLIFIED_NODE_NAMESPACE),
        );
    }

    #[Test]
    public function everyNestingLevelIsIndentedByTwoSpaces(): void
    {
        self::assertSame(<<<'OUTPUT'
            UnionTypeNode
              NamedTypeNode
                Name(int)
              NamedTypeNode
                Name(string)

            OUTPUT, $this->dump([
                new UnionTypeNode([
                    new NamedTypeNode(Name::createFromString('int')),
                    new NamedTypeNode(Name::createFromString('string'))
                ]),
            ]));
    }

    #[Test]
    public function theStringableNodeIsPrintedUsingItsStringValue(): void
    {
        self::assertSame(
            "Name(\\Foo\\Bar)\n",
            $this->dump([Name::createFromString('\\Foo\\Bar')]),
        );
    }

    #[Test]
    public function theNodeWithoutScalarPropertiesHasNoSuffix(): void
    {
        self::assertStringStartsWith(
            "NamedTypeNode\n",
            $this->dump([new NamedTypeNode(Name::createFromString('Example'))]),
        );
    }

    #[Test]
    public function theOffsetPropertyIsNotPrinted(): void
    {
        $node = new NamedTypeNode(Name::createFromString('Example'));
        $node->offset = 42;

        self::assertStringStartsWith("NamedTypeNode\n", $this->dump([$node]));
    }

    #[Test]
    public function theWritableScalarPropertiesArePrinted(): void
    {
        $node = new class extends Node {
            public bool $enabled = true;

            public int $count = 42;

            public string $title = 'example';
        };

        self::assertStringEndsWith("(enabled=true, count=42, title='example')\n", $this->dump([$node]));
    }

    #[Test]
    public function theReadonlyPropertiesAreNotPrinted(): void
    {
        $node = new class extends Node {
            public readonly bool $hidden;

            public bool $visible = true;

            public function __construct()
            {
                $this->hidden = true;

                parent::__construct();
            }
        };

        self::assertStringEndsWith("(visible=true)\n", $this->dump([$node]));
    }

    #[Test]
    public function theStaticPropertiesAreNotPrinted(): void
    {
        $node = new class extends Node {
            public static bool $hidden = true;

            public bool $visible = true;
        };

        self::assertStringEndsWith("(visible=true)\n", $this->dump([$node]));
    }

    #[Test]
    public function theNonScalarPropertiesAreNotPrinted(): void
    {
        $node = new class extends Node {
            /**
             * @var list<mixed>
             */
            public array $items = [];

            public ?string $nothing = null;

            public bool $visible = true;
        };

        self::assertStringEndsWith("(visible=true)\n", $this->dump([$node]));
    }

    #[Test]
    public function theShapeFieldsArePrintedUsingTheirScalarProperties(): void
    {
        self::assertSame(<<<'OUTPUT'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=true)
                  Identifier(foo)
                  NamedTypeNode
                    Name(int)

            OUTPUT, $this->dump([$this->parse('array{foo?: int}')]));
    }

    #[Test]
    public function resetClearsTheOutput(): void
    {
        $visitor = new StringDumperVisitor();
        Traverser::new([$visitor])->traverse([new Identifier('Example')]);

        $visitor->reset();

        self::assertSame('', $visitor->output);
    }

    #[Test]
    public function theOutputIsClearedOnEachTraversal(): void
    {
        $visitor = new StringDumperVisitor();
        $traverser = Traverser::new([$visitor]);

        $traverser->traverse([new Identifier('First')]);
        $traverser->traverse([new Identifier('Second')]);

        self::assertSame("Identifier(Second)\n", $visitor->output);
    }

    #[Test]
    public function theIndentationIsClearedOnEachTraversal(): void
    {
        $visitor = new StringDumperVisitor();
        $traverser = Traverser::new([$visitor]);

        $traverser->traverse([new NamedTypeNode(Name::createFromString('First'))]);
        $traverser->traverse([new Identifier('Second')]);

        self::assertSame("Identifier(Second)\n", $visitor->output);
    }
}
