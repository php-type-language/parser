<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every kind of a type in every place a type may stand in, and the things that
 * stand in no place of a type at all: the wildcard, the variable, the template
 * parameter.
 */
final class PositionsTest extends SyntaxTestCase
{
    private const MARK = '%s';

    /**
     * One type of each kind the grammar knows.
     *
     * @return array<non-empty-string, non-empty-string>
     */
    private static function types(): array
    {
        return [
            'name' => 'Foo',
            'qualified name' => '\Foo\Bar',
            'virtual name' => 'non-empty-string',
            'keyword as a name' => 'is',
            'int' => '42',
            'negative float' => '-1.5e3',
            'string' => "'a'",
            'null' => 'null',
            'this' => '$this',
            'class constant' => 'Foo::BAR',
            'class constant mask' => 'Foo::BAR_*',
            'global constant mask' => 'PHP_*',
            'nullable' => '?int',
            'list' => 'int[]',
            'offset access' => 'T[K]',
            'union' => 'int|string',
            'intersection' => 'A&B',
            'generic' => 'Foo<int, string>',
            'generic with a variance' => 'Foo<covariant int>',
            'shape' => 'array{a: int, b?: string}',
            'unsealed shape' => 'array{a: int, ...<K, V>}',
            'callable' => 'callable(int $a, string ...$b): void',
            'callable with templates' => 'callable<T of int>(T): T',
            'group' => '(int)',
            'condition' => 'T is int ? A : B',
            'negated condition' => '$a is not ?B ? C : D',
        ];
    }

    /**
     * The places where a whole type stands, the weakest level of precedence.
     *
     * @return array<non-empty-string, non-empty-string>
     */
    private static function fullPositions(): array
    {
        return [
            'top' => self::MARK,
            'group' => '(' . self::MARK . ')',
            'argument' => 'Foo<' . self::MARK . '>',
            'second argument' => 'Foo<int, ' . self::MARK . '>',
            'argument with a variance' => 'Foo<covariant ' . self::MARK . '>',
            'shape value' => 'array{a: ' . self::MARK . '}',
            'optional shape value' => 'array{a?: ' . self::MARK . '}',
            'shape value with an int key' => 'array{0: ' . self::MARK . '}',
            'shape field with no key' => 'array{' . self::MARK . '}',
            'unsealed arguments' => 'array{...<' . self::MARK . '>}',
            'parameter' => 'callable(' . self::MARK . ')',
            'second parameter' => 'callable(int, ' . self::MARK . ')',
            'parameter with a name' => 'callable(' . self::MARK . ' $a)',
            'parameter by reference' => 'callable(' . self::MARK . ' &$a)',
            'variadic parameter' => 'callable(' . self::MARK . ' ...$a)',
            'variadic parameter with no name' => 'callable(...' . self::MARK . ')',
            'offset key' => 'T[' . self::MARK . ']',
            'upper bound' => 'callable<T of ' . self::MARK . '>(): void',
            'lower bound' => 'callable<T super ' . self::MARK . '>(): void',
            'default of a template' => 'callable<T = ' . self::MARK . '>(): void',
            'then branch' => 'A is B ? ' . self::MARK . ' : C',
            'else branch' => 'A is B ? C : ' . self::MARK,
        ];
    }

    /**
     * The places where an operand stands: the bare type is read by the
     * precedence of the place, so only the grouped one is the same everywhere.
     *
     * @return array<non-empty-string, non-empty-string>
     */
    private static function operandPositions(): array
    {
        return [
            'union member' => 'A|' . self::MARK,
            'first union member' => self::MARK . '|A',
            'intersection member' => 'A&' . self::MARK,
            'first intersection member' => self::MARK . '&A',
            'nullable' => '?' . self::MARK,
            'list' => self::MARK . '[]',
            'offset access' => self::MARK . '[K]',
            'return type' => 'callable(): ' . self::MARK,
            'subject of a condition' => self::MARK . ' is int ? A : B',
            'target of a condition' => 'A is ' . self::MARK . ' ? B : C',
            'negated target of a condition' => 'A is not ' . self::MARK . ' ? B : C',
        ];
    }

    private static function put(string $template, string $type): string
    {
        return \str_replace(self::MARK, $type, $template);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function typeInFullPositionDataProvider(): iterable
    {
        foreach (self::fullPositions() as $position => $template) {
            foreach (self::types() as $kind => $type) {
                yield $kind . ' as a ' . $position => [self::put($template, $type)];
            }
        }
    }

    #[DataProvider('typeInFullPositionDataProvider')]
    public function testTypeIsAcceptedWhereAWholeTypeStands(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function groupedTypeInOperandPositionDataProvider(): iterable
    {
        foreach (self::operandPositions() as $position => $template) {
            foreach (self::types() as $kind => $type) {
                yield $kind . ' as a ' . $position => [self::put($template, '(' . $type . ')')];
            }
        }
    }

    #[DataProvider('groupedTypeInOperandPositionDataProvider')]
    public function testGroupedTypeIsAcceptedWhereAnOperandStands(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * The wildcard is an argument of a template and nothing else.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function wildcardInAPlaceOfATypeDataProvider(): iterable
    {
        $allowed = ['argument', 'second argument', 'unsealed arguments'];

        foreach ([...self::fullPositions(), ...self::operandPositions()] as $position => $template) {
            if (\in_array($position, $allowed, true)) {
                continue;
            }

            yield 'wildcard as a ' . $position => [self::put($template, '*')];
        }
    }

    #[DataProvider('wildcardInAPlaceOfATypeDataProvider')]
    public function testWildcardIsRejectedWhereAnArgumentOfATemplateDoesNotStand(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function wildcardInAnArgumentDataProvider(): iterable
    {
        yield 'alone' => ['Foo<*>'];
        yield 'first' => ['Foo<*, int>'];
        yield 'last' => ['Foo<int, *>'];
        yield 'among others' => ['Foo<A, *, B>'];
        yield 'twice' => ['Foo<*, *>'];
        yield 'with a trailing comma' => ['Foo<*,>'];
        yield 'with spaces' => ['Foo< * >'];
        yield 'with a comment' => ['Foo</* c */ * /* c */>'];
        yield 'in an argument' => ['Foo<Bar<*>>'];
    }

    #[DataProvider('wildcardInAnArgumentDataProvider')]
    public function testWildcardIsAcceptedAsAnArgumentOfATemplate(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * A variable names a parameter or stands on a side of a condition, it is
     * no type, so it is rejected in the rest of the places.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableInAPlaceOfATypeDataProvider(): iterable
    {
        $allowed = [
            'parameter',
            'second parameter',
            'variadic parameter with no name',
            'subject of a condition',
            'target of a condition',
            'negated target of a condition',
        ];

        foreach ([...self::fullPositions(), ...self::operandPositions()] as $position => $template) {
            if (\in_array($position, $allowed, true)) {
                continue;
            }

            yield 'variable as a ' . $position => [self::put($template, '$a')];
        }
    }

    #[DataProvider('variableInAPlaceOfATypeDataProvider')]
    public function testVariableIsRejectedWhereATypeStands(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * A bound, a default and a variance are written in the list of the
     * parameters of a callable, in no other place.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function templateParameterInAPlaceOfATypeDataProvider(): iterable
    {
        $parameters = [
            'bounded parameter' => 'T of int',
            'parameter with a lower bound' => 'T super int',
            'parameter with a default' => 'T = int',
            'parameter with a variance' => 'covariant T of int',
        ];

        foreach ([...self::fullPositions(), ...self::operandPositions()] as $position => $template) {
            if (\str_contains($template, 'callable<')) {
                continue;
            }

            foreach ($parameters as $kind => $parameter) {
                // A name with a default is a parameter of a callable, the parenthesis is its list
                if ($parameter === 'T = int' && \str_starts_with($position, 'parameter')) {
                    continue;
                }

                if ($parameter === 'T = int' && $position === 'second parameter') {
                    continue;
                }

                yield $kind . ' as a ' . $position => [self::put($template, $parameter)];
            }
        }
    }

    #[DataProvider('templateParameterInAPlaceOfATypeDataProvider')]
    public function testTemplateParameterIsRejectedOutsideOfTheListOfACallable(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * The ellipsis stands in the parameters of a callable and in the tail of
     * a shape.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function ellipsisInAPlaceOfATypeDataProvider(): iterable
    {
        $allowed = [
            'parameter',
            'second parameter',
            'variadic parameter with no name',
            'parameter with a name',
            'shape field with no key',
        ];

        foreach ([...self::fullPositions(), ...self::operandPositions()] as $position => $template) {
            if (\in_array($position, $allowed, true)) {
                continue;
            }

            yield 'ellipsis as a ' . $position => [self::put($template, '...')];
        }
    }

    #[DataProvider('ellipsisInAPlaceOfATypeDataProvider')]
    public function testEllipsisIsRejectedWhereATypeStands(string $source): void
    {
        self::assertRejected($source);
    }
}
