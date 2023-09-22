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
            Stmt\NamedTypeNode
              Name(int)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(0)
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(100)
            AST];
        yield 'int<min, 100>' => ['int<min, 100>', <<<'AST'
            Stmt\NamedTypeNode
              Name(int)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(min)
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(100)
            AST];
        yield 'int<50, max>' => ['int<50, max>', <<<'AST'
            Stmt\NamedTypeNode
              Name(int)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(50)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(max)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#general-arrays */
        yield 'Type[]' => ['Type[]', <<<'AST'
            Stmt\TypesListNode
              Stmt\NamedTypeNode
                Name(Type)
            AST];
        yield 'array<Type>' => ['array<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'array<int, Type>' => ['array<int, Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<Type>' => ['non-empty-array<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(non-empty-array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-array<int, Type>' => ['non-empty-array<int, Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(non-empty-array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#lists */
        yield 'list<Type>' => ['list<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(list)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'non-empty-list<Type>' => ['non-empty-list<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(non-empty-list)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#key-and-value-types-of-arrays-and-iterables */
        yield 'key-of<Type::ARRAY_CONST>' => ['key-of<Type::ARRAY_CONST>', <<<'AST'
            Stmt\NamedTypeNode
              Name(key-of)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\ClassConstNode(ARRAY_CONST)
                    Name(Type)
            AST];
        yield 'value-of<Type::ARRAY_CONST>' => ['value-of<Type::ARRAY_CONST>', <<<'AST'
            Stmt\NamedTypeNode
              Name(value-of)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\ClassConstNode(ARRAY_CONST)
                    Name(Type)
            AST];

        /** @Link https://phpstan.org/writing-php-code/phpdoc-types#iterables */
        yield 'iterable<Type>' => ['iterable<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(iterable)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<Type>' => ['Collection<Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(Collection)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection<int, Type>' => ['Collection<int, Type>', <<<'AST'
            Stmt\NamedTypeNode
              Name(Collection)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Type)
            AST];
        yield 'Collection|Type[]' => ['Collection|Type[]', <<<'AST'
            Stmt\UnionTypeNode
              Stmt\NamedTypeNode
                Name(Collection)
              Stmt\TypesListNode
                Stmt\NamedTypeNode
                  Name(Type)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#union-types */
        yield 'Type1|Type2' => ['Type1|Type2', <<<'AST'
            Stmt\UnionTypeNode
              Stmt\NamedTypeNode
                Name(Type1)
              Stmt\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#intersection-types */
        yield 'Type1&Type2' => ['Type1&Type2', <<<'AST'
            Stmt\IntersectionTypeNode
              Stmt\NamedTypeNode
                Name(Type1)
              Stmt\NamedTypeNode
                Name(Type2)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#parentheses */
        yield '(Type1&Type2)|Type3' => ['(Type1&Type2)|Type3', <<<'AST'
            Stmt\UnionTypeNode
              Stmt\IntersectionTypeNode
                Stmt\NamedTypeNode
                  Name(Type1)
                Stmt\NamedTypeNode
                  Name(Type2)
              Stmt\NamedTypeNode
                Name(Type3)
            AST];

        // Skip
        // https://phpstan.org/writing-php-code/phpdoc-types#generics

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#class-string */
        yield 'class-string' => ['class-string'];
        yield 'class-string<T>' => ['class-string<T>', <<<'AST'
            Stmt\NamedTypeNode
              Name(class-string)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(T)
            AST];
        yield 'class-string<Foo>' => ['class-string<Foo>', <<<'AST'
            Stmt\NamedTypeNode
              Name(class-string)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
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
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(foo)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\StringLiteralNode(foo)
                Stmt\Shape\NamedFieldNode(bar)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(string)
                  Literal\StringLiteralNode(bar)
            AST];
        yield 'array{\'foo\': int, "bar"?: string}' => ['array{\'foo\': int, "bar"?: string}', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(foo)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\StringLiteralNode(foo)
                Stmt\Shape\OptionalFieldNode
                  Stmt\Shape\NamedFieldNode(bar)
                    Stmt\Shape\FieldNode
                      Stmt\NamedTypeNode
                        Name(string)
                    Literal\StringLiteralNode(bar)
            AST];
        yield 'array{int, int}' => ['array{int, int}', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(int)
            AST];
        yield 'array{0: int, 1?: int}' => ['array{0: int, 1?: int}', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NumericFieldNode(0)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\IntLiteralNode(0)
                Stmt\Shape\OptionalFieldNode
                  Stmt\Shape\NumericFieldNode(1)
                    Stmt\Shape\FieldNode
                      Stmt\NamedTypeNode
                        Name(int)
                    Literal\IntLiteralNode(1)
            AST];
        yield 'array{foo: int, bar: string}' => ['array{foo: int, bar: string}', <<<'AST'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(foo)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\StringLiteralNode(foo)
                Stmt\Shape\NamedFieldNode(bar)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(string)
                  Literal\StringLiteralNode(bar)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants */
        yield ' 234' => ['234', 'Literal\IntLiteralNode(234)'];
        yield ' 1.0' => ['1.0', 'Literal\FloatLiteralNode(1.0)'];
        yield '\'foo\'|\'bar\'' => ['\'foo\'|\'bar\'', <<<'AST'
            Stmt\UnionTypeNode
              Literal\StringLiteralNode(foo)
              Literal\StringLiteralNode(bar)
            AST];
        yield 'Foo::SOME_CONSTANT' => ['Foo::SOME_CONSTANT', <<<'AST'
            Stmt\ClassConstNode(SOME_CONSTANT)
              Name(Foo)
            AST];
        yield 'Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT' => ['Foo::SOME_CONSTANT|Bar::OTHER_CONSTANT', <<<'AST'
            Stmt\UnionTypeNode
              Stmt\ClassConstNode(SOME_CONSTANT)
                Name(Foo)
              Stmt\ClassConstNode(OTHER_CONSTANT)
                Name(Bar)
            AST];
        yield 'self::SOME_*' => ['self::SOME_*', <<<'AST'
            Stmt\ClassConstMaskNode(SOME_*)
              Name(self)
            AST];
        yield 'Foo::*' => ['Foo::*', <<<'AST'
            Stmt\ClassConstMaskNode(*)
              Name(Foo)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#global-constants */
        yield 'SOME_CONSTANT' => ['SOME_CONSTANT', <<<'AST'
            Stmt\NamedTypeNode
              Name(SOME_CONSTANT)
            AST];
        yield 'SOME_CONSTANT|OTHER_CONSTANT' => ['SOME_CONSTANT|OTHER_CONSTANT', <<<'AST'
            Stmt\UnionTypeNode
              Stmt\NamedTypeNode
                Name(SOME_CONSTANT)
              Stmt\NamedTypeNode
                Name(OTHER_CONSTANT)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#callables */
        yield 'callable(int, int): string' => ['callable(int, int): string', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Callable\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
              Stmt\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int, int=): string' => ['callable(int, int=): string', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Callable\OptionalArgumentNode
                  Stmt\Callable\ArgumentNode
                    Stmt\NamedTypeNode
                      Name(int)
              Stmt\NamedTypeNode
                Name(string)
            AST];
        yield 'callable(int $foo, string $bar): void' => ['callable(int $foo, string $bar): void', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\NamedArgumentNode($foo)
                  Stmt\Callable\ArgumentNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\VariableLiteralNode(foo)
                Stmt\Callable\NamedArgumentNode($bar)
                  Stmt\Callable\ArgumentNode
                    Stmt\NamedTypeNode
                      Name(string)
                  Literal\VariableLiteralNode(bar)
              Stmt\NamedTypeNode
                Name(void)
            AST];
        yield 'callable(string &$bar): mixed' => ['callable(string &$bar): mixed', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\NamedArgumentNode($bar)
                  Stmt\Callable\OutArgumentNode
                    Stmt\Callable\ArgumentNode
                      Stmt\NamedTypeNode
                        Name(string)
                  Literal\VariableLiteralNode(bar)
              Stmt\NamedTypeNode
                Name(mixed)
            AST];
        yield 'callable(float ...$floats): (int|null)' => ['callable(float ...$floats): (int|null)', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\NamedArgumentNode($floats)
                  Stmt\Callable\VariadicArgumentNode
                    Stmt\Callable\ArgumentNode
                      Stmt\NamedTypeNode
                        Name(float)
                  Literal\VariableLiteralNode(floats)
              Stmt\UnionTypeNode
                Stmt\NamedTypeNode
                  Name(int)
                Literal\NullLiteralNode(null)
            AST];
        yield 'callable(float...): (int|null)' => ['callable(float...): (int|null)', <<<'AST'
            Stmt\CallableTypeNode
              Name(callable)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\VariadicArgumentNode
                  Stmt\Callable\ArgumentNode
                    Stmt\NamedTypeNode
                      Name(float)
              Stmt\UnionTypeNode
                Stmt\NamedTypeNode
                  Name(int)
                Literal\NullLiteralNode(null)
            AST];
        yield '\Closure(int, int): string' => ['\Closure(int, int): string', <<<'AST'
            Stmt\CallableTypeNode
              FullQualifiedName(\Closure)
              Stmt\Callable\ArgumentsListNode
                Stmt\Callable\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Callable\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
              Stmt\NamedTypeNode
                Name(string)
            AST];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#bottom-type */
        yield 'never' => ['never'];
        yield 'never-return' => ['never-return'];
        yield 'never-returns' => ['never-returns'];
        yield 'no-return' => ['no-return'];

        /** @link https://phpstan.org/writing-php-code/phpdoc-types#integer-masks */
        yield 'int-mask<1, 2, 4>' => ['int-mask<1, 2, 4>', <<<'AST'
            Stmt\NamedTypeNode
              Name(int-mask)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(1)
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(2)
                Stmt\Template\ParameterNode
                  Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<1|2|4>' => ['int-mask-of<1|2|4>', <<<'AST'
            Stmt\NamedTypeNode
              Name(int-mask-of)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\UnionTypeNode
                    Literal\IntLiteralNode(1)
                    Stmt\UnionTypeNode
                      Literal\IntLiteralNode(2)
                      Literal\IntLiteralNode(4)
            AST];
        yield 'int-mask-of<Foo::INT_*>' => ['int-mask-of<Foo::INT_*>', <<<'AST'
            Stmt\NamedTypeNode
              Name(int-mask-of)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\ClassConstMaskNode(INT_*)
                    Name(Foo)
            AST];
    }

    #[DataProvider('typesDataProvider')]
    public function testTypes(string $type, string $expected = null): void
    {
        $this->assertStatementSame($type, $expected ?? <<<AST
            Stmt\NamedTypeNode
              Name($type)
            AST);
    }
}
