<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithGroupUsesStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithMethodStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClosureUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\CommentsAroundUsesStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\FunctionAndConstUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\GroupUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\LeadingBackslashUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\MultipleImportsPerStatementStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\MultipleNamespacesClassStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\NoImportsStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\SimpleClassStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\TraitUsageStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\TwoClassesInFileStub;
use TypeLang\Parser\TypeResolver\PhpUseStatementsReader;

final class PhpUseStatementsReaderTest extends TypeResolverTestCase
{
    private ?PhpUseStatementsReader $reader = null;

    /**
     * @param class-string $class
     * @return array<int|non-empty-string, non-empty-string>
     * @throws \ReflectionException
     */
    private function read(string $class): array
    {
        return ($this->reader ??= new PhpUseStatementsReader())->getClassUseStatements(new \ReflectionClass($class));
    }

    /**
     * @return array<int|non-empty-string, non-empty-string>
     */
    private function readFunction(\ReflectionFunctionAbstract $function): array
    {
        return ($this->reader ??= new PhpUseStatementsReader())->getFunctionUseStatements($function);
    }

    /**
     * @throws \ReflectionException
     */
    private function functionStub(): \ReflectionFunction
    {
        require_once __DIR__ . '/Stub/functions.php';

        return new \ReflectionFunction(__NAMESPACE__ . '\Stub\exampleFunctionStub');
    }

    public function testReadsPlainAndAliasedImports(): void
    {
        self::assertSame([
            // use Some\Any;
            'Some\Any',
            // use Some\Any\Test as Example;
            'Example' => 'Some\Any\Test',
        ], $this->read(SimpleClassStub::class));
    }

    public function testReadsAllImportsInSourceOrder(): void
    {
        self::assertSame([
            // use Example\Some\Any\Test1 as Example1;
            'Example1' => 'Example\Some\Any\Test1',
            // use Example\Some\Any1;
            'Example\Some\Any1',
            // use Some\Any\Test2 as Example2;
            'Example2' => 'Some\Any\Test2',
            // use Some\Any2;
            'Some\Any2',
        ], $this->read(ClassWithGroupUsesStub::class));
    }

    public function testReadsImportsOfTheOwningNamespaceBlock(): void
    {
        self::assertSame([
            // use Some\Any\Test2 as Example2;
            'Example2' => 'Some\Any\Test2',
            // use Some\Any2;
            'Some\Any2',
        ], $this->read(MultipleNamespacesClassStub::class));
    }

    public function testReadsImportsOfTheGlobalNamespaceBlock(): void
    {
        // The global \Example class is declared in the same file as the stub
        // below; reflecting the stub forces that file (and \Example) to load.
        new \ReflectionClass(MultipleNamespacesClassStub::class);

        self::assertSame([
            // use Some\Any\Test1 as Example1;
            'Example1' => 'Some\Any\Test1',
            // use Some\Any1;
            'Some\Any1',
        ], $this->read(\Example::class));
    }

    public function testReturnsEmptyForInternalClass(): void
    {
        self::assertSame([], $this->read(\stdClass::class));
    }

    public function testReturnsEmptyForClassWithoutImports(): void
    {
        self::assertSame([], $this->read(NoImportsStub::class));
    }

    public function testIgnoresCommentsAroundImports(): void
    {
        self::assertSame([
            'Some\First',
            'Aliased' => 'Some\Second',
            'Some\Third',
        ], $this->read(CommentsAroundUsesStub::class));
    }

    public function testReadsImportsSharedBySeveralClassesInOneFile(): void
    {
        self::assertSame([
            'Alias' => 'Some\Other',
            'Some\Shared',
        ], $this->read(TwoClassesInFileStub::class));
    }

    public function testIgnoresTraitUsageInsideClassBody(): void
    {
        self::assertSame([
            'Some\ImportedClass',
        ], $this->read(TraitUsageStub::class));
    }

    public function testIgnoresClosureUseClause(): void
    {
        self::assertSame([
            'Some\RealImport',
        ], $this->read(ClosureUseStub::class));
    }

    public function testReadsImportsForClassWithoutNamespaceDeclaration(): void
    {
        require_once __DIR__ . '/Stub/GlobalScopeStub.php';

        if (!\class_exists('GlobalScopeStub')) {
            self::fail('The global-scope stub class was not loaded');
        }

        self::assertSame([
            'Some\Any',
            'Alias' => 'Some\Any\Thing',
        ], $this->read('GlobalScopeStub'));
    }

    public function testExpandsGroupUseStatements(): void
    {
        self::assertSame([
            'Some\Group\First',
            'Alias' => 'Some\Group\Second',
            'Some\Group\Third',
        ], $this->read(GroupUseStub::class));
    }

    public function testReadsFunctionAndConstImports(): void
    {
        self::assertSame([
            // use Some\ClassName;
            'Some\ClassName',
            // use function Some\helperFunction;
            'Some\helperFunction',
            // use const Some\SOME_CONSTANT;
            'Some\SOME_CONSTANT',
            // use Some\Aliased as Alias;
            'Alias' => 'Some\Aliased',
        ], $this->read(FunctionAndConstUseStub::class));
    }

    public function testReadsImportsWrittenWithLeadingBackslash(): void
    {
        self::assertSame([
            'Some\Any',
            'Alias' => 'Some\Any\Thing',
        ], $this->read(LeadingBackslashUseStub::class));
    }

    /**
     * A single `use A, B as C, D;` statement declares several imports at once,
     * each of which must be reported individually.
     */
    public function testReadsMultipleImportsInOneStatement(): void
    {
        self::assertSame([
            // use Some\A,
            'Some\A',
            // Some\B as Bee,
            'Bee' => 'Some\B',
            // Some\C;
            'Some\C',
        ], $this->read(MultipleImportsPerStatementStub::class));
    }

    public function testReadsImportsOfFunction(): void
    {
        self::assertSame([
            // use Some\Any;
            'Some\Any',
            // use Some\Any\Test as Example;
            'Example' => 'Some\Any\Test',
            // use function Some\helper;
            'Some\helper',
        ], $this->readFunction($this->functionStub()));
    }

    /**
     * A method is not a namespaced symbol on its own, so its imports have to
     * be read from the declaring class instead.
     */
    public function testReturnsEmptyForMethod(): void
    {
        self::assertSame(
            [],
            $this->readFunction(new \ReflectionMethod(ClassWithMethodStub::class, 'example')),
        );
    }

    public function testReturnsEmptyForInternalFunction(): void
    {
        self::assertSame([], $this->readFunction(new \ReflectionFunction('strlen')));
    }
}
