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
        yield 'true' => ['true', 'Stmt\Literal\BoolLiteralNode(true)'];
        yield 'false' => ['false', 'Stmt\Literal\BoolLiteralNode(false)'];
        yield 'null' => ['null', 'Stmt\Literal\NullLiteralNode(null)'];
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
            Stmt\Type\NamedTypeNode
              Name(int)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(0)
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(100)
            AST];
        yield 'int<min, 100>' => ['int<min, 100>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(int)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(min)
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(100)
            AST];
        yield 'int<50, max>' => ['int<50, max>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(int)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(50)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(max)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#general-arrays */
        yield 'Type[]' => ['Type[]', <<<'AST'
            Stmt\Type\TypesListNode
              Stmt\Type\NamedTypeNode
                Name(Type)
            AST];
        yield 'array<Type>' => ['array<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'array<int, Type>' => ['array<int, Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<Type>' => ['non-empty-array<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(non-empty-array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<int, Type>' => ['non-empty-array<int, Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(non-empty-array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#lists */
        yield 'list<Type>' => ['list<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(list)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-list<Type>' => ['non-empty-list<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(non-empty-list)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#key-and-value-types-of-arrays-and-iterables */
        yield 'key-of<Type::ARRAY_CONST>' => ['key-of<Type::ARRAY_CONST>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(key-of)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\ClassConstNode(ARRAY_CONST)
                    Name(Type)
            AST];
        yield 'value-of<Type::ARRAY_CONST>' => ['value-of<Type::ARRAY_CONST>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(value-of)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\ClassConstNode(ARRAY_CONST)
                    Name(Type)
            AST];

        /** @Link https://phpstan.org/writing-php-code/phpdoc-types#iterables */
        yield 'iterable<Type>' => ['iterable<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(iterable)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<Type>' => ['Collection<Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(Collection)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<int, Type>' => ['Collection<int, Type>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(Collection)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection|Type[]' => ['Collection|Type[]', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Type\NamedTypeNode
                Name(Collection)
              Stmt\Type\TypesListNode
                Stmt\Type\NamedTypeNode
                  Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#union-types */
        yield 'Type1|Type2' => ['Type1|Type2', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Type\NamedTypeNode
                Name(Type1)
              Stmt\Type\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#intersection-types */
        yield 'Type1&Type2' => ['Type1&Type2', <<<'AST'
            Stmt\Type\IntersectionTypeNode
              Stmt\Type\NamedTypeNode
                Name(Type1)
              Stmt\Type\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#parentheses */
        yield '(Type1&Type2)|Type3' => ['(Type1&Type2)|Type3', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Type\IntersectionTypeNode
                Stmt\Type\NamedTypeNode
                  Name(Type1)
                Stmt\Type\NamedTypeNode
                  Name(Type2)
              Stmt\Type\NamedTypeNode
                Name(Type3)
            AST];

        // Skip
        // https://phpstan.org/writing-php-code/phpdoc-types#generics

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#class-string */
        yield 'class-string' => ['class-string'];
        yield 'class-string<T>' => ['class-string<T>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(class-string)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(T)
            AST];
        yield 'class-string<Foo>' => ['class-string<Foo>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(class-string)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
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
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\NamedFieldNode(foo)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\StringLiteralNode('foo')
                Stmt\Type\Shape\NamedFieldNode(bar)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(string)
                  Stmt\Literal\StringLiteralNode("bar")
            AST];
        yield 'array{\'foo\': int, "bar"?: string}' => ['array{\'foo\': int, "bar"?: string}', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\NamedFieldNode(foo)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\StringLiteralNode('foo')
                Stmt\Type\Shape\OptionalFieldNode
                  Stmt\Type\Shape\NamedFieldNode(bar)
                    Stmt\Type\Shape\FieldNode
                      Stmt\Type\NamedTypeNode
                        Name(string)
                    Stmt\Literal\StringLiteralNode("bar")
            AST];
        yield 'array{int, int}' => ['array{int, int}', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
            AST];
        yield 'array{0: int, 1?: int}' => ['array{0: int, 1?: int}', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\NumericFieldNode(0)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\IntLiteralNode(0)
                Stmt\Type\Shape\OptionalFieldNode
                  Stmt\Type\Shape\NumericFieldNode(1)
                    Stmt\Type\Shape\FieldNode
                      Stmt\Type\NamedTypeNode
                        Name(int)
                    Stmt\Literal\IntLiteralNode(1)
            AST];
        yield 'array{foo: int, bar: string}' => ['array{foo: int, bar: string}', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\NamedFieldNode(foo)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\StringLiteralNode(foo)
                Stmt\Type\Shape\NamedFieldNode(bar)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(string)
                  Stmt\Literal\StringLiteralNode(bar)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants */
        yield ' 234' => ['234', 'Stmt\Literal\IntLiteralNode(234)'];
        yield ' 1.0' => ['1.0', 'Stmt\Literal\FloatLiteralNode(1.0)'];
        yield '\'foo\'|\'bar\'' => ['\'foo\'|\'bar\'', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Literal\StringLiteralNode('foo')
              Stmt\Literal\StringLiteralNode('bar')
            AST];
        yield 'Foo::SOME_CONSTANT' => ['Foo::SOME_CONSTANT', <<<'AST'
            Stmt\Type\ClassConstNode(SOME_CONSTANT)
              Name(Foo)
            AST];
        yield 'Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT' => ['Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Type\ClassConstNode(SOME_CONSTANT)
                Name(Foo)
              Stmt\Type\ClassConstNode(OTHER_CONSTANT)
                Name(Bar)
            AST];
        yield 'self::SOME_*' => ['self::SOME_*', <<<'AST'
            Stmt\Type\ClassConstMaskNode(SOME_*)
              Name(self)
            AST];
        yield 'Foo::*' => ['Foo::*', <<<'AST'
            Stmt\Type\ClassConstMaskNode(*)
              Name(Foo)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#global-constants */
        yield 'SOME_CONSTANT' => ['SOME_CONSTANT', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(SOME_CONSTANT)
            AST];
        yield 'SOME_CONSTANT|OTHER_CONSTANT' => ['SOME_CONSTANT|OTHER_CONSTANT', <<<'AST'
            Stmt\Type\UnionTypeNode
              Stmt\Type\NamedTypeNode
                Name(SOME_CONSTANT)
              Stmt\Type\NamedTypeNode
                Name(OTHER_CONSTANT)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#callables */
        yield 'callable(int, int): string' => ['callable(int, int): string', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\ArgumentNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Callable\ArgumentNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
              Stmt\Type\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int, int=): string' => ['callable(int, int=): string', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\ArgumentNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Callable\OptionalArgumentNode
                  Stmt\Type\Callable\ArgumentNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
              Stmt\Type\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int $foo, string $bar): void' => ['callable(int $foo, string $bar): void', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\NamedArgumentNode($foo)
                  Stmt\Type\Callable\ArgumentNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\VariableLiteralNode($foo)
                Stmt\Type\Callable\NamedArgumentNode($bar)
                  Stmt\Type\Callable\ArgumentNode
                    Stmt\Type\NamedTypeNode
                      Name(string)
                  Stmt\Literal\VariableLiteralNode($bar)
              Stmt\Type\NamedTypeNode
                Name(void)
            AST];
        yield 'callable(string &$bar): mixed' => ['callable(string &$bar): mixed', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\NamedArgumentNode($bar)
                  Stmt\Type\Callable\OutArgumentNode
                    Stmt\Type\Callable\ArgumentNode
                      Stmt\Type\NamedTypeNode
                        Name(string)
                  Stmt\Literal\VariableLiteralNode($bar)
              Stmt\Type\NamedTypeNode
                Name(mixed)
            AST];
        yield 'callable(float ...$floats): (int|null)' => ['callable(float ...$floats): (int|null)', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\NamedArgumentNode($floats)
                  Stmt\Type\Callable\VariadicArgumentNode
                    Stmt\Type\Callable\ArgumentNode
                      Stmt\Type\NamedTypeNode
                        Name(float)
                  Stmt\Literal\VariableLiteralNode($floats)
              Stmt\Type\UnionTypeNode
                Stmt\Type\NamedTypeNode
                  Name(int)
                Stmt\Literal\NullLiteralNode(null)
            AST];
        yield 'callable(float...): (int|null)' => ['callable(float...): (int|null)', <<<'AST'
            Stmt\Type\CallableTypeNode
              Name(callable)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\VariadicArgumentNode
                  Stmt\Type\Callable\ArgumentNode
                    Stmt\Type\NamedTypeNode
                      Name(float)
              Stmt\Type\UnionTypeNode
                Stmt\Type\NamedTypeNode
                  Name(int)
                Stmt\Literal\NullLiteralNode(null)
            AST];
        yield '\Closure(int, int): string' => ['\Closure(int, int): string', <<<'AST'
            Stmt\Type\CallableTypeNode
              FullQualifiedName(\Closure)
              Stmt\Type\Callable\ArgumentsListNode
                Stmt\Type\Callable\ArgumentNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Callable\ArgumentNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
              Stmt\Type\NamedTypeNode
                Name(string)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#bottom-type */
        yield 'never' => ['never'];
        yield 'never-return' => ['never-return'];
        yield 'never-returns' => ['never-returns'];
        yield 'no-return' => ['no-return'];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#integer-masks */
        yield 'int-mask<1, 2, 4>' => ['int-mask<1, 2, 4>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(int-mask)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(1)
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(2)
                Stmt\Type\Template\ParameterNode
                  Stmt\Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<1|2|4>' => ['int-mask-of<1|2|4>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(int-mask-of)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\UnionTypeNode
                    Stmt\Literal\IntLiteralNode(1)
                    Stmt\Type\UnionTypeNode
                      Stmt\Literal\IntLiteralNode(2)
                      Stmt\Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<Foo::INT_*>' => ['int-mask-of<Foo::INT_*>', <<<'AST'
            Stmt\Type\NamedTypeNode
              Name(int-mask-of)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\ClassConstMaskNode(INT_*)
                    Name(Foo)
            AST];
    }

    #[DataProvider('typesDataProvider')]
    public function testTypes(string $type, string $expected = null): void
    {
        $this->assertStatementSame($type, $expected ?? <<<AST
            Stmt\Type\NamedTypeNode
              Name($type)
            AST);
    }
}
