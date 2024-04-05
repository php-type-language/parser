<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Node\FullQualifiedName;
use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Literal\BoolLiteralNode;
use TypeLang\Parser\Node\Literal\FloatLiteralNode;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\LiteralKind;
use TypeLang\Parser\Node\Literal\NullLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Literal\VariableLiteralNode;
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\ParameterNode;
use TypeLang\Parser\Node\Stmt\Callable\ParametersListNode;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ClassConstNode;
use TypeLang\Parser\Node\Stmt\Condition\ConditionKind;
use TypeLang\Parser\Node\Stmt\Condition\EqualConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\NotEqualConditionNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\IntersectionTypeNode;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;
use TypeLang\Parser\Node\Stmt\NullableTypeNode;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Shape\ImplicitFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\NamedFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\NumericFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\ShapeFieldKind;
use TypeLang\Parser\Node\Stmt\Shape\StringNamedFieldNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode;
use TypeLang\Parser\Node\Stmt\TernaryConditionNode;
use TypeLang\Parser\Node\Stmt\TypeKind;
use TypeLang\Parser\Node\Stmt\TypesListNode;
use TypeLang\Parser\Node\Stmt\UnionTypeNode;

#[Group('unit'), Group('type-lang/parser')]
final class SerializationTest extends TestCase
{
    /**
     * @param class-string<\UnitEnum> $enum
     * @return iterable<non-empty-string, array{\UnitEnum}>
     */
    private static function enums(string $enum): iterable
    {
        foreach ($enum::cases() as $case) {
            yield $enum . '::' . $case->name => [$case];
        }
    }

    public static function nodesDataProvider(): iterable
    {
        // Common
        yield Name::class => [new Name('Some\Any')];
        yield FullQualifiedName::class => [new Name('Some\Any')];
        yield Identifier::class => [new Identifier('Some')];

        // Literal
        yield BoolLiteralNode::class => [new BoolLiteralNode(true, 'true')];
        yield FloatLiteralNode::class => [new FloatLiteralNode(0.42, '0.42')];
        yield IntLiteralNode::class => [new IntLiteralNode(42, '42')];
        yield NullLiteralNode::class => [new NullLiteralNode('NulL')];
        yield StringLiteralNode::class => [new StringLiteralNode('0xDEADBEEF', '0xDEADBEEF')];
        yield VariableLiteralNode::class => [new VariableLiteralNode('$some')];
        yield from self::enums(LiteralKind::class);

        // Shapes
        yield FieldsListNode::class => [new FieldsListNode([
            new ImplicitFieldNode(
                type: new NamedTypeNode(new Name('string')),
                optional: true,
            ),
        ], sealed: false)];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(
            type: new NamedTypeNode(new Name('string')),
            optional: true,
        )];
        yield NamedFieldNode::class => [new NamedFieldNode(
            key: new Identifier('string'),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];
        yield NumericFieldNode::class => [new NumericFieldNode(
            key: new IntLiteralNode(42),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];
        yield StringNamedFieldNode::class => [new StringNamedFieldNode(
            key: new StringLiteralNode('key'),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];
        yield from self::enums(ShapeFieldKind::class);

        // Templates
        yield ArgumentsListNode::class => [new ArgumentsListNode([
            new ArgumentNode(
                value: new NamedTypeNode('Some\Any'),
            ),
            new ArgumentNode(
                value: new NamedTypeNode('Any\Test'),
                hint: new Identifier('in'),
            ),
        ])];
        yield ArgumentNode::class => [new ArgumentNode(
            value: new NamedTypeNode('Any\Test'),
            hint: new Identifier('out'),
        )];

        // Statements :: Common
        yield ClassConstMaskNode::class => [new ClassConstMaskNode(
            class: new Name('Example\Class\Name'),
            constant: new Identifier('SOME_'),
        )];
        yield ClassConstNode::class => [new ClassConstNode(
            class: new Name('Example\Class\Name'),
            constant: new Identifier('CONSTANT'),
        )];
        yield ConstMaskNode::class => [new ConstMaskNode(
            name: new Name('Some\Any\CONST_'),
        )];

        yield IntersectionTypeNode::class => [new IntersectionTypeNode(
            new NamedTypeNode(new Name('int')),
            new NamedTypeNode(new Name('float')),
        )];
        yield UnionTypeNode::class => [new IntersectionTypeNode(
            new NamedTypeNode(new Name('int')),
            new NamedTypeNode(new Name('float')),
        )];
        yield TypesListNode::class => [new TypesListNode(
            type: new NamedTypeNode('int'),
        )];
        yield NamedTypeNode::class => [
            new NamedTypeNode(
                name: new Name(new Identifier('int')),
                arguments: new ArgumentsListNode([
                    new ArgumentNode(
                        value: new NamedTypeNode('int'),
                    ),
                    new ArgumentNode(
                        value: new NamedTypeNode('int'),
                        hint: new Identifier('covariant')
                    ),
                ]),
                fields: new FieldsListNode([
                    new ImplicitFieldNode(
                        type: new NamedTypeNode(new Name('string')),
                        optional: true,
                    ),
                    new NamedFieldNode(
                        key: new Identifier('string'),
                        of: new NamedTypeNode(new Name('string')),
                        optional: true,
                    ),
                    new NumericFieldNode(
                        key: new IntLiteralNode(42),
                        of: new NamedTypeNode(new Name('string')),
                        optional: true,
                    ),
                    new StringNamedFieldNode(
                        key: new StringLiteralNode('key'),
                        of: new NamedTypeNode(new Name('string')),
                        optional: true,
                    ),
                ], sealed: false),
            ),
        ];
        yield NullableTypeNode::class => [
            new NullableTypeNode(
                type: new NamedTypeNode('int'),
            ),
        ];

        // Statements :: Ternary
        yield EqualConditionNode::class => [new EqualConditionNode(
            subject: new VariableLiteralNode('$some'),
            target: new NamedTypeNode('int')
        )];
        yield NotEqualConditionNode::class => [new NotEqualConditionNode(
            subject: new VariableLiteralNode('$some'),
            target: new NamedTypeNode('int')
        )];
        yield from self::enums(ConditionKind::class);

        yield TernaryConditionNode::class => [
            new TernaryConditionNode(
                condition: new EqualConditionNode(
                    subject: new VariableLiteralNode('$some'),
                    target: new NamedTypeNode('int')
                ),
                then: new NamedTypeNode('int'),
                else: new NamedTypeNode('mixed')
            ),
        ];

        // Statements :: Callable
        yield ParameterNode::class => [$param = new ParameterNode(
            type: new NamedTypeNode(new Name('int')),
            name: new VariableLiteralNode('$test'),
            output: true,
            variadic: true,
            optional: true,
        )];
        yield ParametersListNode::class => [$paramList = new ParametersListNode(
            items: [$param],
        )];
        yield CallableTypeNode::class => [new CallableTypeNode(
            name: new Name('foo'),
            parameters: $paramList,
            type: new NamedTypeNode(new Name('void'))
        )];

        yield from self::enums(TypeKind::class);
    }

    #[DataProvider('nodesDataProvider')]
    public function testPhpSerialization(object $expected): void
    {
        $actual = \unserialize(\serialize($expected));

        self::assertEquals($expected, $actual);
    }

    private function getPathname(object $node, string $ext): string
    {
        $filename = __DIR__ . '/SerializationTest/'
            . \strtolower(\str_replace('\\', '_', $node::class));

        if ($node instanceof \UnitEnum) {
            $filename .= '.' . \strtolower($node->name);
        }

        return $filename . '.' . $ext;
    }

    #[DataProvider('nodesDataProvider')]
    public function testPhpSerializationSize(object $node): void
    {
        $pathname = $this->getPathname($node, 'txt');

        if (!\is_file($pathname)) {
            \file_put_contents($pathname, \serialize($node));
        }

        $serializedExpected = \trim(\file_get_contents($pathname));
        $serializedActual = \trim(\serialize($node));

        if ($serializedExpected !== $serializedActual) {
            \fwrite(\STDERR, '-- ' . $node::class . " has been changed\n");
            \fwrite(\STDERR, 'Saved: ' . $serializedExpected . "\n");
            \fwrite(\STDERR, 'Actual: ' . $serializedActual . "\n");
        }

        self::assertLessThanOrEqual(
            expected: \strlen($serializedExpected),
            actual: \strlen($serializedActual),
            message: \sprintf(
                'Saved %d bytes, but actual is %d bytes',
                \strlen($serializedExpected),
                \strlen($serializedActual),
            ),
        );

        \file_put_contents($pathname, $serializedActual);
    }

    #[DataProvider('nodesDataProvider')]
    public function testJsonSerialization(object $node): void
    {
        $pathname = $this->getPathname($node, 'json');

        if (!\is_file($pathname)) {
            \file_put_contents(
                filename: $pathname,
                data: \json_encode($node, flags: \JSON_PRETTY_PRINT),
            );
        }

        self::assertSame(
            expected: \file_get_contents($pathname),
            actual: \json_encode($node, flags: \JSON_PRETTY_PRINT),
            message: 'Actual data not compatible with the stored ' . $pathname
        );
    }
}
