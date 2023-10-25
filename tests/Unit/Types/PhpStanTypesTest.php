<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Types;

use PHPUnit\Framework\Attributes\DataProvider;

final class PhpStanTypesTest extends TypesTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, 1?: non-empty-string}>
     */
    public static function typesDataProvider(): iterable
    {
        /** @link https://phpstan.org/writing-php-code/phpdoc-types#basic-types */
        yield 'int' => ['int'];
        yield 'integer' => ['integer'];
        yield 'string' => ['string'];
        yield 'array-key' => ['array-key'];
        yield 'bool' => ['bool'];
        yield 'boolean' => ['boolean'];
        yield 'true' => ['true', 'Literal\BoolLiteralNode(true)'];
        yield 'false' => ['false', 'Literal\BoolLiteralNode(false)'];
        yield 'null' => ['null', 'Literal\NullLiteralNode(null)'];
        yield 'float' => ['float'];
        yield 'double' => ['double'];
        yield 'scalar' => ['scalar'];
        yield 'array' => ['array'];
        yield 'iterable' => ['iterable'];
        yield 'callable' => ['callable'];
        yield 'resource' => ['resource'];
        yield 'void' => ['void'];
        yield 'object' => ['object'];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#integer-ranges */
        yield 'positive-int' => ['positive-int'];
        yield 'negative-int' => ['negative-int'];
        yield 'non-positive-int' => ['non-positive-int'];
        yield 'non-negative-int' => ['non-negative-int'];
        yield 'non-zero-int' => ['non-zero-int'];
        yield 'int<0, 100>' => ['int<0, 100>', <<<'AST'
            Type\NamedTypeNode
              Name(int)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(0)
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(100)
            AST];
        yield 'int<min, 100>' => ['int<min, 100>', <<<'AST'
            Type\NamedTypeNode
              Name(int)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(min)
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(100)
            AST];
        yield 'int<50, max>' => ['int<50, max>', <<<'AST'
            Type\NamedTypeNode
              Name(int)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(50)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(max)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#general-arrays */
        yield 'Type[]' => ['Type[]', <<<'AST'
            Type\TypesListNode
              Type\NamedTypeNode
                Name(Type)
            AST];
        yield 'array<Type>' => ['array<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'array<int, Type>' => ['array<int, Type>', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<Type>' => ['non-empty-array<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(non-empty-array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<int, Type>' => ['non-empty-array<int, Type>', <<<'AST'
            Type\NamedTypeNode
              Name(non-empty-array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#lists */
        yield 'list<Type>' => ['list<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(list)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-list<Type>' => ['non-empty-list<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(non-empty-list)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#key-and-value-types-of-arrays-and-iterables */
        yield 'key-of<Type::ARRAY_CONST>' => ['key-of<Type::ARRAY_CONST>', <<<'AST'
            Type\NamedTypeNode
              Name(key-of)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\ClassConstNode
                    Name(Type)
                    Identifier(ARRAY_CONST)
            AST];
        yield 'value-of<Type::ARRAY_CONST>' => ['value-of<Type::ARRAY_CONST>', <<<'AST'
            Type\NamedTypeNode
              Name(value-of)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\ClassConstNode
                    Name(Type)
                    Identifier(ARRAY_CONST)
            AST];

        /** @Link https://phpstan.org/writing-php-code/phpdoc-types#iterables */
        yield 'iterable<Type>' => ['iterable<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(iterable)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<Type>' => ['Collection<Type>', <<<'AST'
            Type\NamedTypeNode
              Name(Collection)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<int, Type>' => ['Collection<int, Type>', <<<'AST'
            Type\NamedTypeNode
              Name(Collection)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection|Type[]' => ['Collection|Type[]', <<<'AST'
            Type\UnionTypeNode
              Type\NamedTypeNode
                Name(Collection)
              Type\TypesListNode
                Type\NamedTypeNode
                  Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#union-types */
        yield 'Type1|Type2' => ['Type1|Type2', <<<'AST'
            Type\UnionTypeNode
              Type\NamedTypeNode
                Name(Type1)
              Type\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#intersection-types */
        yield 'Type1&Type2' => ['Type1&Type2', <<<'AST'
            Type\IntersectionTypeNode
              Type\NamedTypeNode
                Name(Type1)
              Type\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#parentheses */
        yield '(Type1&Type2)|Type3' => ['(Type1&Type2)|Type3', <<<'AST'
            Type\UnionTypeNode
              Type\IntersectionTypeNode
                Type\NamedTypeNode
                  Name(Type1)
                Type\NamedTypeNode
                  Name(Type2)
              Type\NamedTypeNode
                Name(Type3)
            AST];

        // Skip
        // https://phpstan.org/writing-php-code/phpdoc-types#generics

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#class-string */
        yield 'class-string' => ['class-string'];
        yield 'class-string<T>' => ['class-string<T>', <<<'AST'
            Type\NamedTypeNode
              Name(class-string)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(T)
            AST];
        yield 'class-string<Foo>' => ['class-string<Foo>', <<<'AST'
            Type\NamedTypeNode
              Name(class-string)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Foo)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#other-advanced-string-types */
        yield 'callable-string' => ['callable-string'];
        yield 'numeric-string' => ['numeric-string'];
        yield 'non-empty-string' => ['non-empty-string'];
        yield 'non-falsy-string' => ['non-falsy-string'];
        yield 'truthy-string' => ['truthy-string'];
        yield 'literal-string' => ['literal-string'];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#array-shapes */
        yield 'array{\'foo\': int, "bar": string}' => ['array{\'foo\': int, "bar": string}', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\StringNamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Literal\StringLiteralNode('foo')
                Type\Shape\StringNamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(string)
                  Literal\StringLiteralNode("bar")
            AST];
        yield 'array{\'foo\': int, "bar"?: string}' => ['array{\'foo\': int, "bar"?: string}', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\StringNamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Literal\StringLiteralNode('foo')
                Type\Shape\StringNamedFieldNode(optional)
                  Type\NamedTypeNode
                    Name(string)
                  Literal\StringLiteralNode("bar")
            AST];
        yield 'array{int, int}' => ['array{int, int}', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
            AST];
        yield 'array{0: int, 1?: int}' => ['array{0: int, 1?: int}', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NumericFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Literal\IntLiteralNode(0)
                Type\Shape\NumericFieldNode(optional)
                  Type\NamedTypeNode
                    Name(int)
                  Literal\IntLiteralNode(1)
            AST];
        yield 'array{foo: int, bar: string}' => ['array{foo: int, bar: string}', <<<'AST'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Identifier(foo)
                Type\Shape\NamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(string)
                  Identifier(bar)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants */
        yield ' 234' => ['234', 'Literal\IntLiteralNode(234)'];
        yield ' 1.0' => ['1.0', 'Literal\FloatLiteralNode(1.0)'];
        yield '\'foo\'|\'bar\'' => ['\'foo\'|\'bar\'', <<<'AST'
            Type\UnionTypeNode
              Literal\StringLiteralNode('foo')
              Literal\StringLiteralNode('bar')
            AST];
        yield 'Foo::SOME_CONSTANT' => ['Foo::SOME_CONSTANT', <<<'AST'
            Type\ClassConstNode
              Name(Foo)
              Identifier(SOME_CONSTANT)
            AST];
        yield 'Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT' => ['Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT', <<<'AST'
            Type\UnionTypeNode
              Type\ClassConstNode
                Name(Foo)
                Identifier(SOME_CONSTANT)
              Type\ClassConstNode
                Name(Bar)
                Identifier(OTHER_CONSTANT)
            AST];
        yield 'self::SOME_*' => ['self::SOME_*', <<<'AST'
            Type\ClassConstMaskNode
              Name(self)
              Identifier(SOME_)
            AST];
        yield 'Foo::*' => ['Foo::*', <<<'AST'
            Type\ClassConstMaskNode
              Name(Foo)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#global-constants */
        yield 'SOME_CONSTANT' => ['SOME_CONSTANT', <<<'AST'
            Type\NamedTypeNode
              Name(SOME_CONSTANT)
            AST];
        yield 'SOME_CONSTANT|OTHER_CONSTANT' => ['SOME_CONSTANT|OTHER_CONSTANT', <<<'AST'
            Type\UnionTypeNode
              Type\NamedTypeNode
                Name(SOME_CONSTANT)
              Type\NamedTypeNode
                Name(OTHER_CONSTANT)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#callables */
        yield 'callable(int, int): string' => ['callable(int, int): string', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\ArgumentNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Callable\ArgumentNode
                  Type\NamedTypeNode
                    Name(int)
              Type\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int, int=): string' => ['callable(int, int=): string', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\ArgumentNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Callable\OptionalArgumentNode
                  Type\Callable\ArgumentNode
                    Type\NamedTypeNode
                      Name(int)
              Type\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int $foo, string $bar): void' => ['callable(int $foo, string $bar): void', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\NamedArgumentNode($foo)
                  Type\Callable\ArgumentNode
                    Type\NamedTypeNode
                      Name(int)
                  Literal\VariableLiteralNode($foo)
                Type\Callable\NamedArgumentNode($bar)
                  Type\Callable\ArgumentNode
                    Type\NamedTypeNode
                      Name(string)
                  Literal\VariableLiteralNode($bar)
              Type\NamedTypeNode
                Name(void)
            AST];
        yield 'callable(string &$bar): mixed' => ['callable(string &$bar): mixed', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\NamedArgumentNode($bar)
                  Type\Callable\OutArgumentNode
                    Type\Callable\ArgumentNode
                      Type\NamedTypeNode
                        Name(string)
                  Literal\VariableLiteralNode($bar)
              Type\NamedTypeNode
                Name(mixed)
            AST];
        yield 'callable(float ...$floats): (int|null)' => ['callable(float ...$floats): (int|null)', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\NamedArgumentNode($floats)
                  Type\Callable\VariadicArgumentNode
                    Type\Callable\ArgumentNode
                      Type\NamedTypeNode
                        Name(float)
                  Literal\VariableLiteralNode($floats)
              Type\UnionTypeNode
                Type\NamedTypeNode
                  Name(int)
                Literal\NullLiteralNode(null)
            AST];
        yield 'callable(float...): (int|null)' => ['callable(float...): (int|null)', <<<'AST'
            Type\CallableTypeNode
              Name(callable)
              Type\Callable\ArgumentsListNode
                Type\Callable\VariadicArgumentNode
                  Type\Callable\ArgumentNode
                    Type\NamedTypeNode
                      Name(float)
              Type\UnionTypeNode
                Type\NamedTypeNode
                  Name(int)
                Literal\NullLiteralNode(null)
            AST];
        yield '\Closure(int, int): string' => ['\Closure(int, int): string', <<<'AST'
            Type\CallableTypeNode
              FullQualifiedName(\Closure)
              Type\Callable\ArgumentsListNode
                Type\Callable\ArgumentNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Callable\ArgumentNode
                  Type\NamedTypeNode
                    Name(int)
              Type\NamedTypeNode
                Name(string)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#bottom-type */
        yield 'never' => ['never'];
        yield 'never-return' => ['never-return'];
        yield 'never-returns' => ['never-returns'];
        yield 'no-return' => ['no-return'];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#integer-masks */
        yield 'int-mask<1, 2, 4>' => ['int-mask<1, 2, 4>', <<<'AST'
            Type\NamedTypeNode
              Name(int-mask)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(1)
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(2)
                Type\Template\ParameterNode
                  Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<1|2|4>' => ['int-mask-of<1|2|4>', <<<'AST'
            Type\NamedTypeNode
              Name(int-mask-of)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\UnionTypeNode
                    Literal\IntLiteralNode(1)
                    Literal\IntLiteralNode(2)
                    Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<Foo::INT_*>' => ['int-mask-of<Foo::INT_*>', <<<'AST'
            Type\NamedTypeNode
              Name(int-mask-of)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\ClassConstMaskNode
                    Name(Foo)
                    Identifier(INT_)
            AST];
    }

    #[DataProvider('typesDataProvider')]
    public function testTypes(string $type, string $expected = null): void
    {
        $this->assertStatementSame($type, $expected ?? <<<AST
            Type\NamedTypeNode
              Name($type)
            AST);
    }
}
