<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\TypeResolver\PhpUseStatementsReader\NamespaceFinder;

/**
 * Tests for {@see NamespaceFinder} that moves a token stream to the beginning
 * of the import section of the requested namespace.
 */
#[Group('unit'), Group('type-lang/parser')]
final class NamespaceFinderTest extends TypeResolverTestCase
{
    /**
     * @return \Iterator<array-key, \PhpToken>
     */
    private function rewind(string $namespace, string $source): \Iterator
    {
        $tokens = new \ArrayIterator(\PhpToken::tokenize("<?php\n" . $source));

        return (new NamespaceFinder())->rewind($namespace, $tokens);
    }

    /**
     * Returns the text of the first meaningful token the stream stopped at.
     */
    private function stoppedAt(string $namespace, string $source): ?string
    {
        $tokens = $this->rewind($namespace, $source);

        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id !== \T_WHITESPACE) {
                return $current->text;
            }

            $tokens->next();
        }

        return null;
    }

    /**
     * Returns every qualified name available after the stream position.
     *
     * @return list<non-empty-string>
     */
    private function qualifiedNamesAfter(string $namespace, string $source): array
    {
        $tokens = $this->rewind($namespace, $source);
        $result = [];

        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_NAME_QUALIFIED) {
                $result[] = $current->text;
            }

            $tokens->next();
        }

        return $result;
    }

    #[Test]
    public function theStreamIsMovedAfterTheMatchingNamespace(): void
    {
        self::assertSame('use', $this->stoppedAt('App', <<<'PHP'
            namespace App;

            use Some\Any;
            PHP));
    }

    #[Test]
    public function theNestedNamespaceNameIsMatched(): void
    {
        self::assertSame('use', $this->stoppedAt('App\\Domain', <<<'PHP'
            namespace App\Domain;

            use Some\Any;
            PHP));
    }

    #[Test]
    public function theNonMatchingNamespaceExhaustsTheStream(): void
    {
        self::assertNull($this->stoppedAt('Other', <<<'PHP'
            namespace App;

            use Some\Any;
            PHP));
    }

    #[Test]
    public function theRequestedNamespaceIsFoundAmongSeveralOnes(): void
    {
        self::assertSame(['Second\\Import'], $this->qualifiedNamesAfter('Second', <<<'PHP'
            namespace First;

            use First\Import;

            namespace Second;

            use Second\Import;
            PHP));
    }

    #[Test]
    public function theBracedNamespaceNameIsTerminatedByTheOpeningBrace(): void
    {
        self::assertSame(['Some\\Any'], $this->qualifiedNamesAfter('App', <<<'PHP'
            namespace App {
                use Some\Any;
            }
            PHP));
    }

    #[Test]
    public function theGlobalNamespaceIsMatchedByAnEmptyName(): void
    {
        self::assertSame(['Some\\Any'], $this->qualifiedNamesAfter('', <<<'PHP'
            namespace {
                use Some\Any;
            }
            PHP));
    }

    /**
     * A source without any namespace declaration belongs to the global one, so
     * its import section starts at the very first "use" statement.
     */
    #[Test]
    public function theFirstImportIsFoundInASourceWithoutNamespaces(): void
    {
        self::assertSame('use', $this->stoppedAt('', <<<'PHP'
            use Some\Any;
            PHP));
    }

    #[Test]
    public function theSourceWithoutImportsExhaustsTheStream(): void
    {
        self::assertNull($this->stoppedAt('Other', <<<'PHP'
            final class Example {}
            PHP));
    }
}
