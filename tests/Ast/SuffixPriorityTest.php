<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * How `?`, `[]` and `[K]` bind to one another. A tree is written down with
 * every group in parentheses: `?(T[])` is a nullable of a list, `(?T)[]` is
 * a list of nullables, `T[K]` is `(T)[K]`, the members of an intersection are
 * in curly brackets (`{A&?(B)}`) and the members of a union are in square ones
 * (`[A|?(B)]`).
 *
 * The suffixes bind tighter than the question mark, the question mark binds
 * tighter than the ampersand, the ampersand binds tighter than the bar:
 * `?T[]` is `?(T[])`, `?A&B` is `(?A)&B`, `?A|B` is `(?A)|B`.
 */
final class SuffixPriorityTest extends TestCase
{
    private static function render(TypeNode $node): string
    {
        return match (true) {
            $node instanceof NamedTypeNode => $node->name->toString(),
            $node instanceof ThisTypeNode => '$this',
            $node instanceof UnionTypeNode => '[' . \implode('|', \array_map(self::render(...), $node->children)) . ']',
            $node instanceof IntersectionTypeNode => '{' . \implode('&', \array_map(self::render(...), $node->children)) . '}',
            $node instanceof NullableTypeNode => '?(' . self::render($node->type) . ')',
            $node instanceof TypesListTypeNode => '(' . self::render($node->type) . ')[]',
            $node instanceof TypeOffsetAccessTypeNode => '('
                . self::render($node->type)
                . ')['
                . self::render($node->access)
                . ']',
            default => $node::class,
        };
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function treeDataProvider(): iterable
    {
        // Lists
        yield 'list' => ['T[]', '(T)[]'];
        yield 'two lists' => ['T[][]', '((T)[])[]'];
        yield 'three lists' => ['T[][][]', '(((T)[])[])[]'];
        yield 'list of this' => ['$this[]', '($this)[]'];
        yield 'list with spaces' => ['T [ ] [ ]', '((T)[])[]'];

        // Offset accesses
        yield 'access' => ['T[U]', '(T)[U]'];
        yield 'two accesses' => ['T[U][V]', '((T)[U])[V]'];
        yield 'three accesses' => ['T[U][V][W]', '(((T)[U])[V])[W]'];
        yield 'access by an access' => ['T[U[V]]', '(T)[(U)[V]]'];
        yield 'access by a list' => ['T[U[]]', '(T)[(U)[]]'];
        yield 'access by an access by a list' => ['T[U[V[]]]', '(T)[(U)[(V)[]]]'];
        yield 'access with spaces' => ['T [ U ]', '(T)[U]'];

        // Lists and accesses together
        yield 'list then access' => ['T[][U]', '((T)[])[U]'];
        yield 'access then list' => ['T[U][]', '((T)[U])[]'];
        yield 'list, access, list' => ['T[][U][]', '(((T)[])[U])[]'];
        yield 'access, list, access' => ['T[U][][V]', '(((T)[U])[])[V]'];
        yield 'list in the key and a list' => ['T[U[]][]', '((T)[(U)[]])[]'];

        // The question mark reaches over the suffixes
        yield 'nullable' => ['?T', '?(T)'];
        yield 'nullable list' => ['?T[]', '?((T)[])'];
        yield 'nullable two lists' => ['?T[][]', '?(((T)[])[])'];
        yield 'nullable access' => ['?T[U]', '?((T)[U])'];
        yield 'nullable two accesses' => ['?T[U][V]', '?(((T)[U])[V])'];
        yield 'nullable list and access' => ['?T[][U]', '?(((T)[])[U])'];
        yield 'nullable access and list' => ['?T[U][]', '?(((T)[U])[])'];

        // Groups turn the order over
        yield 'list of nullables' => ['(?T)[]', '(?(T))[]'];
        yield 'two lists of nullables' => ['(?T)[][]', '((?(T))[])[]'];
        yield 'access of nullables' => ['(?T)[U]', '(?(T))[U]'];
        yield 'nullable of a list in a group' => ['?(T[])', '?((T)[])'];
        yield 'nullable of an access in a group' => ['?(T[U])', '?((T)[U])'];
        yield 'group of a nullable list' => ['(?T[])', '?((T)[])'];
        yield 'nullable of a group and a list' => ['?(T)[]', '?((T)[])'];
        yield 'nullable of a group and an access' => ['?(T)[U]', '?((T)[U])'];
        yield 'nullable of a nullable in a group' => ['?(?T)', '?(?(T))'];
        yield 'list of a nullable of a nullable' => ['(?(?T))[]', '(?(?(T)))[]'];
        yield 'group of a nullable and a nullable list' => ['?((?T)[])', '?((?(T))[])'];
        yield 'nullable of a nullable list in a group' => ['?(?T[])', '?(?((T)[]))'];
        yield 'nullable of a nullable in a key' => ['T[?(?K)]', '(T)[?(?(K))]'];
        yield 'nullable of a nullable union in a group' => ['?(?A|B)', '?([?(A)|B])'];

        // The question mark in the key
        yield 'nullable key' => ['T[?U]', '(T)[?(U)]'];
        yield 'nullable key of a nullable' => ['?T[?U]', '?((T)[?(U)])'];
        yield 'nullable key with a list' => ['T[?U[]]', '(T)[?((U)[])]'];
        yield 'nullable key with an access' => ['T[?U[V]]', '(T)[?((U)[V])]'];
        yield 'nullable key with a nullable key' => ['T[?U[?V]]', '(T)[?((U)[?(V)])]'];
        yield 'nullable keys' => ['T[?U][?V]', '((T)[?(U)])[?(V)]'];
        yield 'nullable keys of a nullable' => ['?T[?U][?V]', '?(((T)[?(U)])[?(V)])'];
        yield 'nullable key in a group' => ['T[(?U)]', '(T)[?(U)]'];
        yield 'nullable key list in a group' => ['T[(?U)[]]', '(T)[(?(U))[]]'];
        yield 'nullable key then a list' => ['T[?U][]', '((T)[?(U)])[]'];
        yield 'list then a nullable key' => ['T[][?U]', '((T)[])[?(U)]'];
        yield 'nullable of a nullable key in groups' => ['?(T[?U])', '?((T)[?(U)])'];
        yield 'list of nullables with a nullable key' => ['(?T[?U])[]', '(?((T)[?(U)]))[]'];

        // The ampersand is weaker than the question mark and the suffixes
        yield 'intersection' => ['A&B', '{A&B}'];
        yield 'intersection of three' => ['A&B&C', '{A&B&C}'];
        yield 'nullable on the left' => ['?A&B', '{?(A)&B}'];
        yield 'nullable group on the right' => ['A&(?B)', '{A&?(B)}'];
        yield 'nullable on the right' => ['A&?B', '{A&?(B)}'];
        yield 'nullable in the middle' => ['A&?B&C', '{A&?(B)&C}'];
        yield 'nullable on the last' => ['A&B&?C', '{A&B&?(C)}'];
        yield 'nullable list on the right' => ['A&?B[]', '{A&?((B)[])}'];
        yield 'nullable in an intersection in a union' => ['A&?B|C', '[{A&?(B)}|C]'];
        yield 'nullable on the right of a union' => ['A|B&?C', '[A|{B&?(C)}]'];
        yield 'nullable behind a union behind an intersection' => ['A&B|?C', '[{A&B}|?(C)]'];
        yield 'nullables on both sides' => ['?A&?B', '{?(A)&?(B)}'];
        yield 'nullable list on the left' => ['?A[]&B', '{?((A)[])&B}'];
        yield 'nullable access on the left' => ['?A[K]&B', '{?((A)[K])&B}'];
        yield 'list on the right' => ['A&B[]', '{A&(B)[]}'];
        yield 'access on the right' => ['A&B[K]', '{A&(B)[K]}'];
        yield 'lists on both sides' => ['A[]&B[]', '{(A)[]&(B)[]}'];
        yield 'everything' => ['?A[]&B[?K]&?C', '{?((A)[])&(B)[?(K)]&?(C)}'];
        yield 'nullable of a group of an intersection' => ['?(A&B)', '?({A&B})'];
        yield 'nullable of a group of an intersection and a type' => ['?(A&B)&C', '{?({A&B})&C}'];
        yield 'group of a nullable' => ['(?A)&B', '{?(A)&B}'];
        yield 'list of an intersection' => ['(A&B)[]', '({A&B})[]'];
        yield 'list of an intersection and a type' => ['(A&B)[]&C', '{({A&B})[]&C}'];
        yield 'access of an intersection' => ['(A&B)[K]', '({A&B})[K]'];
        yield 'intersection in a key' => ['T[A&B]', '(T)[{A&B}]'];
        yield 'intersection with a nullable in a key' => ['T[?A&B]', '(T)[{?(A)&B}]'];
        yield 'intersection in a key of a key' => ['T[K[A&B]]', '(T)[(K)[{A&B}]]'];
        yield 'nullable intersection in a key' => ['?T[A&?B]', '?((T)[{A&?(B)}])'];
        yield 'groups of intersections are merged' => ['(A&B)&C', '{A&B&C}'];
        yield 'groups of intersections on the right are merged' => ['A&(B&C)', '{A&B&C}'];

        // The bar is the weakest operator
        yield 'bar: union' => ['A|B', '[A|B]'];
        yield 'bar: union of three' => ['A|B|C', '[A|B|C]'];
        yield 'bar: union of ten' => ['A|B|C|D|E|F|G|H|I|J', '[A|B|C|D|E|F|G|H|I|J]'];
        yield 'bar: intersection then union' => ['A&B|C', '[{A&B}|C]'];
        yield 'bar: union then intersection' => ['A|B&C', '[A|{B&C}]'];
        yield 'bar: intersections in a union' => ['A&B|C&D', '[{A&B}|{C&D}]'];
        yield 'bar: intersection in the middle' => ['A|B&C|D', '[A|{B&C}|D]'];
        yield 'bar: long intersection then union' => ['A&B&C|D', '[{A&B&C}|D]'];
        yield 'bar: union of long intersections' => ['A&B&C|D&E&F', '[{A&B&C}|{D&E&F}]'];
        yield 'bar: group of a union in an intersection' => ['(A|B)&C', '{[A|B]&C}'];
        yield 'bar: group of a union on the right of an intersection' => ['A&(B|C)', '{A&[B|C]}'];
        yield 'bar: groups of unions in an intersection' => ['(A|B)&(C|D)', '{[A|B]&[C|D]}'];
        yield 'bar: group of an intersection in a union' => ['(A&B)|C', '[{A&B}|C]'];
        yield 'bar: groups of unions are merged' => ['(A|B)|C', '[A|B|C]'];
        yield 'bar: groups of unions on the right are merged' => ['A|(B|C)', '[A|B|C]'];
        yield 'bar: groups of unions on both sides are merged' => ['(A|B)|(C|D)', '[A|B|C|D]'];

        // The suffixes are tighter than the bar
        yield 'bar: list in a union' => ['A|B[]', '[A|(B)[]]'];
        yield 'bar: list on the left of a union' => ['A[]|B', '[(A)[]|B]'];
        yield 'bar: lists in a union' => ['A[]|B[]', '[(A)[]|(B)[]]'];
        yield 'bar: lists of lists in a union' => ['A[][]|B[][]', '[((A)[])[]|((B)[])[]]'];
        yield 'bar: access in a union' => ['A|B[K]', '[A|(B)[K]]'];
        yield 'bar: accesses in a union' => ['A[K]|B[L]', '[(A)[K]|(B)[L]]'];
        yield 'bar: mix in a union' => ['A[]|B[K]|C', '[(A)[]|(B)[K]|C]'];
        yield 'bar: list of a union' => ['(A|B)[]', '([A|B])[]'];
        yield 'bar: list of lists of a union' => ['(A|B)[][]', '(([A|B])[])[]'];
        yield 'bar: access of a union' => ['(A|B)[K]', '([A|B])[K]'];
        yield 'bar: list of a union of lists' => ['(A[]|B[])[]', '([(A)[]|(B)[]])[]'];
        yield 'bar: list of an intersection in a union' => ['(A&B)[]|C', '[({A&B})[]|C]'];

        // A union is a key
        yield 'bar: union in a key' => ['T[A|B]', '(T)[[A|B]]'];
        yield 'bar: union with a list in a key' => ['T[A|B[]]', '(T)[[A|(B)[]]]'];
        yield 'bar: union with an intersection in a key' => ['T[A&B|C]', '(T)[[{A&B}|C]]'];
        yield 'bar: unions in keys' => ['T[A|B][C|D]', '((T)[[A|B]])[[C|D]]'];
        yield 'bar: union in a key of a key' => ['T[K[A|B]]', '(T)[(K)[[A|B]]]'];
        yield 'bar: union of accesses with unions' => ['T[A]|U[B|C]', '[(T)[A]|(U)[[B|C]]]'];
        yield 'bar: list of a key with a union' => ['T[A|B][]', '((T)[[A|B]])[]'];

        // The question mark reaches over the member behind it only
        yield 'bar: nullable union' => ['?A|B', '[?(A)|B]'];
        yield 'bar: nullable union of three' => ['?A|B|C', '[?(A)|B|C]'];
        yield 'bar: nullable union with a list' => ['?A[]|B', '[?((A)[])|B]'];
        yield 'bar: nullable union with an intersection on the left' => ['?A&B|C', '[{?(A)&B}|C]'];
        yield 'bar: nullable union with an intersection on the right' => ['?A|B&C', '[?(A)|{B&C}]'];
        yield 'bar: nullables' => ['?A|?B', '[?(A)|?(B)]'];
        yield 'bar: nullable of a group of a union' => ['?(A|B)', '?([A|B])'];
        yield 'bar: nullable of a group of a union and a type' => ['?(A|B)|C', '[?([A|B])|C]'];
        yield 'bar: group of a nullable in a union' => ['(?A)|B', '[?(A)|B]'];
        yield 'bar: groups of nullables in a union' => ['(?A)|(?B)', '[?(A)|?(B)]'];
        yield 'bar: nullable key with a union' => ['T[?A|B]', '(T)[[?(A)|B]]'];
        yield 'bar: list of a nullable union' => ['(?A|B)[]', '([?(A)|B])[]'];
        yield 'bar: nullable of a list of a group of a union' => ['?(A|B)[]', '?(([A|B])[])'];

        // A member behind a bar has its own mark
        yield 'bar: nullable on the right' => ['A|?B', '[A|?(B)]'];
        yield 'bar: nullable in the middle' => ['A|?B|C', '[A|?(B)|C]'];
        yield 'bar: nullable on the last' => ['A|B|?C', '[A|B|?(C)]'];
        yield 'bar: nullables behind the bars' => ['A|?B|?C', '[A|?(B)|?(C)]'];
        yield 'bar: nullable list on the right' => ['A|?B[]', '[A|?((B)[])]'];
        yield 'bar: nullable in an intersection on the right' => ['A|?B&C', '[A|{?(B)&C}]'];
        yield 'bar: nullables in an intersection' => ['A|?B&?C', '[A|{?(B)&?(C)}]'];
        yield 'bar: nullable as a member of an intersection' => ['A|B&?C', '[A|{B&?(C)}]'];
        yield 'bar: nullable between intersections' => ['A&B|?C&D', '[{A&B}|{?(C)&D}]'];
        yield 'bar: nullable in an intersection on the left' => ['A&?B|C', '[{A&?(B)}|C]'];
        yield 'bar: nullable on the right of an intersection' => ['A&B|?C', '[{A&B}|?(C)]'];
        yield 'bar: nullable group on the right' => ['A|(?B)', '[A|?(B)]'];
        yield 'bar: nullable list in a group on the right' => ['A|(?B[])', '[A|?((B)[])]'];
        yield 'bar: nullable of everything' => ['?A[]|B[?K]&?C', '[?((A)[])|{(B)[?(K)]&?(C)}]'];
        yield 'bar: everything' => ['?A&B|C&D[K]|?E[]', '[{?(A)&B}|{C&(D)[K]}|?((E)[])]'];

        // Trivia
        yield 'bar: union with spaces' => ['A | B | C', '[A|B|C]'];
        yield 'bar: union with line breaks' => ["A\n|\nB", '[A|B]'];
        yield 'bar: union with comments' => ['A/* a */|/* b */B', '[A|B]'];
        yield 'bar: nullable union with comments' => ['?/* a */A/* b */|/* c */?/* d */B', '[?(A)|?(B)]'];
        // Spaces and trivia do not change the order
        yield 'nullable with a space' => ['? T []', '?((T)[])'];
        yield 'nullable with line breaks' => ["?\nT\n[\n?\nU\n]", '?((T)[?(U)])'];
        yield 'nullable with comments' => ['?/* a */T/* b */[/* c */?/* d */U/* e */]', '?((T)[?(U)])'];
        yield 'group of a nullable with comments' => ['(/* a */?/* b */T/* c */)[]', '(?(T))[]'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $expected
     */
    #[DataProvider('treeDataProvider')]
    public function testSuffixesAndQuestionMarkBindInTheirOrder(string $source, string $expected): void
    {
        self::assertSame($expected, self::render($this->parse($source)));
    }

    /**
     * Two ways to write the same tree.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function equivalentDataProvider(): iterable
    {
        yield 'group around a name' => ['(T)', 'T'];
        yield 'groups around a name' => ['(((T)))', 'T'];
        yield 'group around a list' => ['(T[])', 'T[]'];
        yield 'group around an access' => ['(T[U])', 'T[U]'];
        yield 'group around a nullable' => ['(?T)', '?T'];
        yield 'group around a nullable list' => ['(?T[])', '?T[]'];
        yield 'group around a nullable access' => ['(?T[U])', '?T[U]'];
        yield 'group around a nullable key' => ['(T[?U])', 'T[?U]'];
        yield 'group around a name before the suffix' => ['(T)[]', '(T)[]'];
        yield 'nullable of a group before the suffix' => ['?(T)[]', '?T[]'];
        yield 'nullable of a group of a list' => ['?(T[])', '?T[]'];
        yield 'nullable of a group of an access' => ['?(T[U])', '?T[U]'];
        yield 'nullable union and union of a nullable' => ['?A|B', '(?A)|B'];
        yield 'nullable intersection and intersection of a nullable' => ['?A&B', '(?A)&B'];
        yield 'nullable member of a union and of a group' => ['A|?B', 'A|(?B)'];
        yield 'nullable member of an intersection and of a group' => ['A&?B', 'A&(?B)'];
        yield 'group around a key' => ['T[(U)]', 'T[U]'];
        yield 'group around a nullable inside of a key' => ['T[(?U)]', 'T[?U]'];
        yield 'group around a list key' => ['T[(U[])]', 'T[U[]]'];
        yield 'spaces' => ['  ?  T  [  ?  U  ]  ', '?T[?U]'];
        yield 'group around a union' => ['(A|B)', 'A|B'];
        yield 'groups around a union' => ['(((A|B)))', 'A|B'];
        yield 'groups of unions' => ['(A|B)|C', 'A|(B|C)'];
        yield 'groups of unions and the plain one' => ['A|(B|C)', 'A|B|C'];
        yield 'group of an intersection in a union' => ['(A&B)|C', 'A&B|C'];
        yield 'group of an intersection on the right' => ['A|(B&C)', 'A|B&C'];
        yield 'group around a member of a union' => ['(A)|(B)', 'A|B'];
        yield 'nullable of a group and a union' => ['?(A)|B', '?A|B'];
        yield 'nullable in a group around a union' => ['(?A|B)', '?A|B'];
        yield 'group around a key with a union' => ['T[(A|B)]', 'T[A|B]'];
        yield 'group of a union before a suffix' => ['((A|B))[]', '(A|B)[]'];
        yield 'spaces and a union' => ['  ?  A  |  ?  B  ', '?A|?B'];
        yield 'comments and a union' => ['/* a */?/* b */A/* c */|/* d */?/* e */B', '?A|?B'];
        yield 'groups of intersections' => ['(A&B)&C', 'A&(B&C)'];
        yield 'groups of intersections and the plain one' => ['A&(B&C)', 'A&B&C'];
        yield 'group around a member' => ['(A)&(B)', 'A&B'];
        yield 'group around an intersection' => ['(A&B)', 'A&B'];
        yield 'nullable of a group and a type' => ['?(A)&B', '?A&B'];
        yield 'spaces and an intersection' => [' ? A [ ] & ? B ', '?A[]&?B'];
        yield 'comments' => ['/* a */?/* b */T/* c */[/* d */?/* e */U/* f */]', '?T[?U]'];
    }

    /**
     * @param non-empty-string $first
     * @param non-empty-string $second
     */
    #[DataProvider('equivalentDataProvider')]
    public function testDifferentWritingsGiveTheSameTree(string $first, string $second): void
    {
        self::assertSame(self::render($this->parse($second)), self::render($this->parse($first)));
    }

    /**
     * Two ways to write trees that are NOT the same, the order is what makes them different.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function differentDataProvider(): iterable
    {
        yield 'nullable list and list of a nullable' => ['?T[]', '(?T)[]'];
        yield 'nullable access and access of a nullable' => ['?T[U]', '(?T)[U]'];
        yield 'list then access and access then list' => ['T[][U]', 'T[U][]'];
        yield 'nullable key and nullable access' => ['T[?U]', '?T[U]'];
        yield 'nullable of an intersection and an intersection of a nullable' => ['?(A&B)', '(?A)&B'];
        yield 'intersection of lists and a list of an intersection' => ['A[]&B[]', '(A&B)[]'];
        yield 'list of an intersection and an intersection of a list' => ['(A&B)[]', 'A&B[]'];
        yield 'union of lists and a list of a union' => ['A[]|B[]', '(A|B)[]'];
        yield 'list of a union and a union of a list' => ['(A|B)[]', 'A|B[]'];
        yield 'union of an intersection and an intersection of a union' => ['A&B|C', 'A&(B|C)'];
        yield 'union then intersection and intersection then union' => ['A|B&C', 'A&B|C'];
        yield 'nullable of a union and union of a nullable' => ['?(A|B)', '(?A)|B'];
        yield 'nullable union and nullable member' => ['?A|B', 'A|?B'];
        yield 'nullable member and nullable intersection' => ['A|?B&C', 'A|B&?C'];
        yield 'nullable member of an intersection and of a union' => ['A|B&?C', '?A|B&C'];
        yield 'union in a key and a union of accesses' => ['T[A|B]', 'T[A]|T[B]'];
        yield 'nullable on the left and on the right' => ['?A&B', 'A&?B'];
        yield 'nullable key list and list of nullable keys' => ['T[?U[]]', 'T[?U][]'];
        yield 'nullable of a nullable key and a nullable key' => ['?T[?U]', 'T[?U]'];
    }

    /**
     * @param non-empty-string $first
     * @param non-empty-string $second
     */
    #[DataProvider('differentDataProvider')]
    public function testOrderMakesTheTreesDifferent(string $first, string $second): void
    {
        self::assertNotSame(self::render($this->parse($second)), self::render($this->parse($first)));
    }
}
