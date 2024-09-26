<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Serialization;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Node\FullQualifiedName;
use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Literal\BoolLiteralNode;
use TypeLang\Parser\Node\Literal\FloatLiteralNode;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\NullLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Literal\VariableLiteralNode;
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\ParameterNode;
use TypeLang\Parser\Node\Stmt\Callable\ParametersListNode;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ClassConstNode;
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
use TypeLang\Parser\Node\Stmt\Shape\StringNamedFieldNode;
use TypeLang\Parser\Node\Stmt\Template\TemplateArgumentNode;
use TypeLang\Parser\Node\Stmt\Template\TemplateArgumentsListNode;
use TypeLang\Parser\Node\Stmt\TernaryConditionNode;
use TypeLang\Parser\Node\Stmt\TypesListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\Node\Stmt\UnionTypeNode;
use TypeLang\Parser\Tests\Unit\TestCase;

#[Group('unit'), Group('type-lang/parser')]
abstract class SerializationTestCase extends TestCase
{
    protected function getPathname(object $node, string $ext): string
    {
        $filename = __DIR__ . '/'
            . self::className(static::class) . '/'
            . \strtolower(\str_replace('\\', '_', $node::class));

        if ($node instanceof \UnitEnum) {
            $filename .= '.' . \strtolower($node->name);
        }

        return $filename . '.' . $ext;
    }

    private static function className(string|object $ctx): string
    {
        return (new \ReflectionClass($ctx))
            ->getShortName();
    }

    public static function nodesDataProvider(): iterable
    {
        // Common
        yield self::className(Name::class) => [
            new Name('Some\Any'),
        ];
        yield self::className(FullQualifiedName::class) => [
            new Name('Some\Any'),
        ];
        yield self::className(Identifier::class) => [
            new Identifier('Some'),
        ];

        // Literal
        yield self::className(BoolLiteralNode::class) => [
            new BoolLiteralNode(true, 'true'),
        ];
        yield self::className(FloatLiteralNode::class) => [
            new FloatLiteralNode(0.42, '0.42'),
        ];
        yield self::className(IntLiteralNode::class) => [
            new IntLiteralNode(42, '42'),
        ];
        yield self::className(NullLiteralNode::class) => [
            new NullLiteralNode('NulL'),
        ];
        yield self::className(StringLiteralNode::class) => [
            new StringLiteralNode('0xDEADBEEF', '0xDEADBEEF'),
        ];
        yield self::className(VariableLiteralNode::class) => [
            new VariableLiteralNode('$some'),
        ];

        // Shapes
        yield self::className(FieldsListNode::class) => [new FieldsListNode([
            new ImplicitFieldNode(
                type: new NamedTypeNode(new Name('string')),
                optional: true,
            ),
        ], sealed: false)];

        yield self::className(ImplicitFieldNode::class) => [new ImplicitFieldNode(
            type: new NamedTypeNode(new Name('string')),
            optional: true,
        )];

        yield self::className(NamedFieldNode::class) => [new NamedFieldNode(
            key: new Identifier('string'),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];

        yield self::className(NumericFieldNode::class) => [new NumericFieldNode(
            key: new IntLiteralNode(42),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];

        yield self::className(StringNamedFieldNode::class) => [new StringNamedFieldNode(
            key: new StringLiteralNode('key'),
            of: new NamedTypeNode(new Name('string')),
            optional: true,
        )];

        // Templates
        yield self::className(TemplateArgumentsListNode::class) => [new TemplateArgumentsListNode([
            new TemplateArgumentNode(
                value: new NamedTypeNode('Some\Any'),
            ),
            new TemplateArgumentNode(
                value: new NamedTypeNode('Any\Test'),
                hint: new Identifier('in'),
            ),
        ])];

        yield self::className(TemplateArgumentNode::class) => [new TemplateArgumentNode(
            value: new NamedTypeNode('Any\Test'),
            hint: new Identifier('out'),
        )];

        yield self::className(TemplateArgumentNode::class)
            . ' without hint' => [new TemplateArgumentNode(
                value: new NamedTypeNode('Any\Test'),
                hint: new Identifier('out'),
            )];

        // Statements :: Common
        yield self::className(ClassConstMaskNode::class) => [new ClassConstMaskNode(
            class: new Name('Example\Class\Name'),
            constant: new Identifier('SOME_'),
        )];

        yield self::className(ClassConstMaskNode::class)
            . ' without prefix' => [new ClassConstMaskNode(
                class: new Name('Example\Class\Name'),
            )];

        yield self::className(ClassConstNode::class) => [new ClassConstNode(
            class: new Name('Example\Class\Name'),
            constant: new Identifier('CONSTANT'),
        )];

        yield self::className(ConstMaskNode::class) => [new ConstMaskNode(
            name: new Name('Some\Any\CONST_'),
        )];

        yield self::className(IntersectionTypeNode::class) => [new IntersectionTypeNode(
            new NamedTypeNode(new Name('int')),
            new NamedTypeNode(new Name('float')),
        )];

        yield self::className(UnionTypeNode::class) => [new UnionTypeNode(
            new NamedTypeNode(new Name('int')),
            new NamedTypeNode(new Name('float')),
        )];

        yield self::className(TypesListNode::class) => [new TypesListNode(
            type: new NamedTypeNode('int'),
        )];

        yield self::className(NamedTypeNode::class) => [
            new NamedTypeNode(
                name: new Name(new Identifier('int')),
                arguments: $arguments = new TemplateArgumentsListNode([
                    new TemplateArgumentNode(
                        value: new NamedTypeNode('int'),
                    ),
                    new TemplateArgumentNode(
                        value: new NamedTypeNode('int'),
                        hint: new Identifier('covariant')
                    ),
                ]),
                fields: $fields = new FieldsListNode([
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

        yield self::className(NamedTypeNode::class)
            . ' without arguments' => [
                new NamedTypeNode(
                    name: new Name(new Identifier('int')),
                    fields: $fields,
                ),
            ];

        yield self::className(NamedTypeNode::class)
            . ' without fields' => [
                new NamedTypeNode(
                    name: new Name(new Identifier('int')),
                    arguments: $arguments,
                ),
            ];

        yield self::className(NamedTypeNode::class)
            . ' without arguments and fields' => [
                new NamedTypeNode(
                    name: new Name(new Identifier('int')),
                ),
            ];

        yield self::className(NullableTypeNode::class) => [
            new NullableTypeNode(
                type: new NamedTypeNode('int'),
            ),
        ];

        // Statements :: Ternary
        yield self::className(EqualConditionNode::class) => [new EqualConditionNode(
            subject: new VariableLiteralNode('$some'),
            target: new NamedTypeNode('int')
        )];

        yield self::className(NotEqualConditionNode::class) => [new NotEqualConditionNode(
            subject: new VariableLiteralNode('$some'),
            target: new NamedTypeNode('int')
        )];

        yield self::className(TernaryConditionNode::class) => [
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
        yield self::className(ParameterNode::class) => [$param = new ParameterNode(
            type: new NamedTypeNode(new Name('int')),
            name: new VariableLiteralNode('$test'),
            output: true,
            variadic: true,
            optional: true,
        )];

        yield self::className(ParameterNode::class)
            . ' without name' => [$param = new ParameterNode(
                type: new NamedTypeNode(new Name('int')),
            )];

        yield self::className(ParametersListNode::class) => [$paramList = new ParametersListNode(
            items: [$param],
        )];

        yield self::className(CallableTypeNode::class) => [new CallableTypeNode(
            name: new Name('foo'),
            parameters: $paramList,
            type: new NamedTypeNode(new Name('void'))
        )];

        yield self::className(CallableTypeNode::class)
            . ' without return type' => [new CallableTypeNode(
                name: new Name('foo'),
                parameters: $paramList,
            )];
    }
}
