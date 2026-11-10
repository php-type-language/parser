<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName\NamespaceKind;

/**
 * A mask of global constants, with a namespace or with none.
 */
final class ConstMaskTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, string|null, bool, non-empty-string}>
     */
    public static function maskDataProvider(): iterable
    {
        yield 'suffix' => ['PHP_*', null, false, 'PHP_*'];
        yield 'prefix' => ['*_SUFFIX', null, false, '*_SUFFIX'];
        yield 'inside' => ['PHP_*_MAX', null, false, 'PHP_*_MAX'];
        yield 'both ends' => ['*A*', null, false, '*A*'];
        yield 'a word and a star' => ['Foo*', null, false, 'Foo*'];
        yield 'fully qualified' => ['\\PHP_*', '\\', true, 'PHP_*'];
        yield 'fully qualified root' => ['\\*', '\\', true, '*'];
        yield 'fully qualified root with a suffix' => ['\\*_SUFFIX', '\\', true, '*_SUFFIX'];
        yield 'namespaced' => ['Foo\\Bar\\BAZ_*', 'Foo\\Bar', false, 'BAZ_*'];
        yield 'namespaced and fully qualified' => ['\\Foo\\BAR_*', '\\Foo', true, 'BAR_*'];
        yield 'whole namespace' => ['Path\\To\\*', 'Path\\To', false, '*'];
        yield 'whole one word namespace' => ['Path\\*', 'Path', false, '*'];
        yield 'whole namespace with a suffix' => ['Path\\To\\*_SUFFIX', 'Path\\To', false, '*_SUFFIX'];
        yield 'whole fully qualified namespace' => ['\\Path\\To\\*', '\\Path\\To', true, '*'];
        yield 'current namespace' => ['namespace\\BAR_*', 'namespace', false, 'BAR_*'];
        yield 'relative to the current namespace' => ['namespace\\Foo\\BAR_*', 'namespace\\Foo', false, 'BAR_*'];
        yield 'whole current namespace' => ['namespace\\*', 'namespace', false, '*'];
        yield 'word of a literal' => ['true*', null, false, 'true*'];
        yield 'digits behind a star' => ['A_*_1', null, false, 'A_*_1'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $mask
     */
    #[DataProvider('maskDataProvider')]
    public function testMaskIsBuilt(string $source, ?string $namespace, bool $isFullyQualified, string $mask): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(ConstMaskTypeNode::class, $type);
        self::assertSame($namespace, $type->owner?->toString());
        self::assertSame($isFullyQualified, $type->owner?->kind === NamespaceKind::FullyQualified);
        self::assertSame($mask, $type->mask->toString());
    }

    public function testWordsAndStarsOfAMaskAreKeptInTheirKinds(): void
    {
        $type = $this->parse('Foo\\A*B*');

        self::assertInstanceOf(ConstMaskTypeNode::class, $type);
        self::assertSame(
            [Identifier::class, WildcardMaskNode::class, Identifier::class, WildcardMaskNode::class],
            \array_map(static fn(object $item): string => $item::class, $type->mask->children),
        );
        self::assertSame(['A', 'B'], $type->mask->getSegmentsAsStrings());
    }

    /**
     * Each part is a pair of a text and an offset. The offset of the type is where its first character stands.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, list<array{string, int<0, max>}>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'suffix' => ['PHP_*', 0, [['PHP_', 0], ['*', 4]]];
        yield 'spaces before' => ['  PHP_*', 2, [['PHP_', 2], ['*', 6]]];
        yield 'prefix' => ['*_SUFFIX', 0, [['*', 0], ['_SUFFIX', 1]]];
        yield 'fully qualified' => ['\\PHP_*', 0, [['PHP_', 1], ['*', 5]]];
        yield 'fully qualified root' => ['\\*', 0, [['*', 1]]];
        yield 'fully qualified root with a suffix' => ['\\*_X', 0, [['*', 1], ['_X', 2]]];
        yield 'namespaced' => ['Foo\\Bar\\BAZ_*', 0, [['BAZ_', 8], ['*', 12]]];
        yield 'namespaced and fully qualified' => ['\\Foo\\BAR_*', 0, [['BAR_', 5], ['*', 9]]];
        yield 'whole namespace' => ['Path\\To\\*', 0, [['*', 8]]];
        yield 'whole namespace with a suffix' => ['Path\\To\\*_X', 0, [['*', 8], ['_X', 9]]];
        yield 'comment before' => ['/* c */ Path\\*', 8, [['*', 13]]];
        yield 'multibyte before' => ['/* Фу */ PHP_*', 11, [['PHP_', 11], ['*', 15]]];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     * @param list<array{string, int<0, max>}> $parts
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $offset, array $parts): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(ConstMaskTypeNode::class, $type);
        self::assertSame($offset, $type->offset);
        self::assertSame($parts[0][1], $type->mask->offset);
        self::assertSame(
            $parts,
            \array_map(static fn(object $item): array => [$item->toString(), $item->offset], $type->mask->children),
        );
    }

    public function testNamespaceOfAMaskKeepsItsOffset(): void
    {
        $type = $this->parse('  Foo\\Bar\\BAZ_*');

        self::assertInstanceOf(ConstMaskTypeNode::class, $type);
        self::assertSame(2, $type->owner?->offset);
    }

    public function testTriviaAroundIsIgnored(): void
    {
        $type = $this->parse("/* a */ PHP_* // b\n");

        self::assertInstanceOf(ConstMaskTypeNode::class, $type);
        self::assertSame('PHP_*', $type->mask->toString());
    }

    public function testNameWithNoStarStaysAName(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('PHP_EOL'));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('Path\\To\\Foo'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function invalidDataProvider(): iterable
    {
        yield 'lone star' => ['*'];
        yield 'two stars' => ['PHP_**'];
        yield 'two stars in a namespace' => ['Path\\To\\**'];
        yield 'namespace with no star' => ['Path\\To\\'];
        yield 'separator alone' => ['\\'];
        yield 'constant of a mask' => ['PHP_*::X'];
        yield 'number with a star' => ['1*'];
        yield 'spaces around a star' => ['PHP_ * MAX'];
        yield 'space in a namespaced mask' => ['Path\\To\\ *'];
        yield 'this with a star' => ['$this*'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('invalidDataProvider')]
    public function testInvalidMaskIsNotBuilt(string $source): void
    {
        try {
            $this->parse($source);
        } catch (\Throwable $e) {
            self::assertNotInstanceOf(\Error::class, $e);

            return;
        }

        self::fail(\sprintf('The source %s must not be parsed', \json_encode($source, \JSON_THROW_ON_ERROR)));
    }
}
