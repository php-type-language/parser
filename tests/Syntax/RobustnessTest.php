<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use Phplrt\Contracts\Parser\Exception\ParserExceptionInterface as PhplrtParserExceptionInterface;
use TypeLang\Parser\Exception\ParserExceptionInterface;

/**
 * Whatever the source is, the parser either reads it or refuses it the way a
 * parser refuses, and it does so in the time that depends on the length of
 * the source and not on how deep it is nested.
 */
final class RobustnessTest extends SyntaxTestCase
{
    private const MUTATIONS = 60;

    /**
     * What is put into a source by a mutation.
     */
    private const PIECES = [
        '(', ')', '<', '>', '{', '}', '[', ']', ',', ':', '::', '?', '|', '&', '*', '...', '=', '$a', '\\', ' ',
        "\n", '/*', '*/', '#', '//', 'is', 'not', 'is not', 'of', 'super', 'covariant', '1', "'", '"', '-', 'T', 'int',
    ];

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function sourceDataProvider(): iterable
    {
        yield 'name' => ['\Foo\Bar'];
        yield 'virtual name' => ['non-empty-string'];
        yield 'literals' => ["42|-1.5e3|0x1F|'a'|\"b\\n\"|null|true"];
        yield 'constants' => ['Foo::BAR|Foo::BAR_*|PHP_*|\Path\To\*'];
        yield 'nullable and suffixes' => ['?T[K][]|(?A)[]'];
        yield 'logical' => ['A&B|C&(D|E)'];
        yield 'generics' => ['Foo<covariant A, *, Bar<int, B>,>'];
        yield 'shape' => ['array{a: int, b?: string, 0: A, Foo::BAR: B, ...<K, V>}'];
        yield 'sealed shape' => ['Foo{int, string,}'];
        yield 'callable' => ['callable(int $a = 42, &$b, ...$c, string &...$d,): ?A[]'];
        yield 'callable with templates' => ['callable<covariant T of A|B super C = D, U>(T $a): U'];
        yield 'condition' => ['T is int ? A : B is not ?C ? D : E'];
        yield 'condition with variables' => ['$a is $b ? (C is D ? E : F) : $this'];
        yield 'conditions in places' => ['Foo<T is int ? A : B>|array{a: T is not int ? A : B}'];
        yield 'trivia' => ["int /* c */ | // c\n string # c\n"];
        yield 'groups' => ['(((A|B)&C)[])'];
    }

    private static function next(int &$state): int
    {
        $state = ($state * 1103515245 + 12345) & 0x7fffffff;

        return $state >> 8;
    }

    private static function mutate(string $source, int &$state): string
    {
        $length = \strlen($source);
        $piece = self::PIECES[self::next($state) % \count(self::PIECES)];
        $at = $length === 0 ? 0 : self::next($state) % $length;

        return match (self::next($state) % 6) {
            0 => \substr($source, 0, $at) . \substr($source, $at + 1),
            1 => \substr($source, 0, $at) . $piece . \substr($source, $at),
            2 => \substr($source, 0, $at),
            3 => \substr($source, $at),
            4 => \substr($source, 0, $at) . ($source[$at] ?? '') . \substr($source, $at),
            default => \substr($source, 0, $at) . $piece . \substr($source, $at + 1),
        };
    }

    /**
     * @param non-empty-string $source
     * @throws \Throwable
     */
    #[DataProvider('sourceDataProvider')]
    public function testMutatedSourceIsReadOrRefusedByTheParser(string $source): void
    {
        $state = \crc32($source);

        for ($i = 0; $i < self::MUTATIONS; ++$i) {
            $mutated = self::mutate(self::mutate($source, $state), $state);

            try {
                $this->parse($mutated);
            } catch (\Throwable $e) {
                self::assertTrue(
                    $e instanceof ParserExceptionInterface || $e instanceof PhplrtParserExceptionInterface,
                    \sprintf(
                        'The source %s is refused with %s: %s',
                        \json_encode($mutated, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
                        $e::class,
                        $e->getMessage(),
                    ),
                );
            }
        }
    }

    /**
     * The part that is repeated, the part that stands inside of all of them
     * and the part that closes them.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, string}>
     */
    public static function nestingDataProvider(): iterable
    {
        yield 'groups' => ['(', 'A', ')'];
        yield 'groups of unions' => ['(A|', 'B', ')'];
        yield 'groups of intersections' => ['(A&', 'B', ')'];
        yield 'nullable groups' => ['?(', 'A', ')'];
        yield 'arguments' => ['Foo<', 'A', '>'];
        yield 'arguments with a variance' => ['Foo<covariant ', 'A', '>'];
        yield 'shapes' => ['array{a: ', 'A', '}'];
        yield 'unsealed shapes' => ['array{a: ', 'A', ', ...}'];
        yield 'parameters' => ['callable(', 'A', ')'];
        yield 'returns in groups' => ['(callable(): ', 'A', ')'];
        yield 'templates' => ['callable<T of ', 'A', '>(): void'];
        yield 'offsets' => ['T[', 'K', ']'];
        yield 'lists' => ['', 'A', '[][][]'];
        yield 'condition in the else branch' => ['A is B ? C : ', 'D', ''];
        yield 'condition in the then branch' => ['A is B ? ', 'C', ' : D'];
        yield 'negated condition in the else branch' => ['A is not B ? C : ', 'D', ''];
        yield 'condition in a subject' => ['(', 'A is B ? C : D', ') is E ? F : G'];
        yield 'condition in a group' => ['A is B ? (', 'C', ') : D'];
    }

    /**
     * Twice the depth is twice the time. A source that is read by trying one
     * way and then another doubles the work at every level, so the depths go
     * up until the first of them that takes too long and the test stops there.
     *
     * @param non-empty-string $open
     * @param non-empty-string $inside
     * @throws \Throwable
     */
    #[DataProvider('nestingDataProvider')]
    public function testNestingIsReadInTimeThatDoesNotGrowByTheDepth(string $open, string $inside, string $close): void
    {
        $limit = 0.25;

        foreach ([4, 8, 12, 16, 20, 24, 28, 32, 40, 60] as $depth) {
            $source = \str_repeat($open, $depth) . $inside . \str_repeat($close, $depth);

            foreach ([$source, $source . ' junk', $source . ')'] as $variant) {
                $start = \hrtime(true);

                try {
                    $this->parse($variant);
                } catch (\Throwable) {
                    // A refusal is as good as a reading, the time is what is measured
                }

                $seconds = (\hrtime(true) - $start) / 1e9;

                self::assertLessThan(
                    $limit,
                    $seconds,
                    \sprintf('The depth %d of %s takes %.2f s', $depth, \json_encode($variant), $seconds),
                );
            }
        }
    }
}
