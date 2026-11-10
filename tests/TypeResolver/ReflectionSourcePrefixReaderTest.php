<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithMethodStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\SimpleClassStub;
use TypeLang\Parser\TypeResolver\PhpUseStatementsReader\ReflectionSourcePrefixReader;

/**
 * Tests for {@see ReflectionSourcePrefixReader} that reads the source of
 * a file up to the line where the reflected entity is declared.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ReflectionSourcePrefixReaderTest extends TypeResolverTestCase
{
    private ReflectionSourcePrefixReader $reader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reader = new ReflectionSourcePrefixReader();
    }

    /**
     * @throws \ReflectionException
     */
    private function functionStub(): \ReflectionFunction
    {
        require_once __DIR__ . '/Stub/functions.php';

        return new \ReflectionFunction(__NAMESPACE__ . '\Stub\exampleFunctionStub');
    }

    #[Test]
    public function theClassHeaderContainsTheImports(): void
    {
        $header = $this->reader->readClassHeader(new \ReflectionClass(SimpleClassStub::class));

        self::assertStringContainsString('use Some\Any;', $header);
        self::assertStringContainsString('use Some\Any\Test as Example;', $header);
    }

    #[Test]
    public function theClassHeaderDoesNotContainTheDeclarationItself(): void
    {
        $header = $this->reader->readClassHeader(new \ReflectionClass(SimpleClassStub::class));

        self::assertStringNotContainsString('final class SimpleClassStub', $header);
    }

    #[Test]
    public function theClassHeaderIsEmptyForAnInternalClass(): void
    {
        self::assertSame('', $this->reader->readClassHeader(new \ReflectionClass(\stdClass::class)));
    }

    #[Test]
    public function theFunctionHeaderContainsTheImports(): void
    {
        $header = $this->reader->readFunctionHeader($this->functionStub());

        self::assertStringContainsString('use Some\Any;', $header);
        self::assertStringContainsString('use function Some\helper;', $header);
    }

    #[Test]
    public function theFunctionHeaderDoesNotContainTheDeclarationItself(): void
    {
        $header = $this->reader->readFunctionHeader($this->functionStub());

        self::assertStringNotContainsString('function exampleFunctionStub', $header);
    }

    #[Test]
    public function theFunctionHeaderIsEmptyForAnInternalFunction(): void
    {
        self::assertSame('', $this->reader->readFunctionHeader(new \ReflectionFunction('strlen')));
    }

    #[Test]
    public function theMethodHeaderIsReadFromItsOwnFile(): void
    {
        $header = $this->reader->readFunctionHeader(
            new \ReflectionMethod(ClassWithMethodStub::class, 'example'),
        );

        self::assertStringContainsString('use Some\Method\Any;', $header);
    }

    #[Test]
    public function theHeaderStartsAtTheBeginningOfTheFile(): void
    {
        $header = $this->reader->readClassHeader(new \ReflectionClass(SimpleClassStub::class));

        self::assertStringStartsWith('<?php', $header);
    }
}
