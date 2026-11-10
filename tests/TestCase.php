<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use JetBrains\PhpStorm\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Partial\ParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Validation\CheckResult;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

/**
 * @phpstan-type ParserOptionsType array{
 *     unions?: bool,
 *     intersections?: bool,
 *     nullables?: bool,
 *     lists?: bool,
 *     offsets?: bool,
 *     conditions?: bool,
 *     generics?: bool,
 *     wildcards?: bool,
 *     hints?: bool,
 *     callables?: bool,
 *     templates?: bool,
 *     defaults?: bool,
 *     shapes?: bool,
 *     constants?: bool,
 *     masks?: bool,
 *     literals?: bool,
 * }
 */
#[Group('unit'), Group('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
    private ?TypeParserInterface $parser = null;

    /**
     * @param ParserOptionsType $options
     */
    protected function parser(array $options = []): TypeParserInterface
    {
        if ($options === []) {
            return $this->parser ??= new TypeParser();
        }

        return new TypeParser(new TypeParserFeatures(...$options));
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parse(#[Language('PHP')] string $code, array $options = []): TypeNode
    {
        $parser = $this->parser($options);

        return $parser->parse($code);
    }

    /**
     * Returns the exception the parser fails on the given source with.
     *
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parseFailure(#[Language('PHP')] string $code, array $options = []): ParserExceptionInterface
    {
        try {
            $this->parse($code, $options);
        } catch (ParserExceptionInterface $e) {
            return $e;
        }

        self::fail(\sprintf('The source "%s" must not be parsed', $code));
    }

    /**
     * Reads a name the same way the parser does: `Foo`, `Foo\Bar`, `\Foo\Bar`.
     *
     * @param non-empty-string $name
     * @throws \Throwable
     */
    protected function parseName(string $name): Name
    {
        $type = $this->parse($name);

        \assert($type instanceof NamedTypeNode);

        return $type->name;
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function partial(#[Language('PHP')] string $code, array $options = []): ParsedResult
    {
        $parser = $this->parser($options);

        return $parser->partial($code);
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function validate(#[Language('PHP')] string $code, array $options = []): CheckResult
    {
        $parser = $this->parser($options);

        return $parser->validate($code);
    }

    protected function print(TypeNode $statement): string
    {
        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$statement]);

        return \trim($visitor->output);
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parseAndPrint(#[Language('PHP')] string $code, array $options = []): string
    {
        $type = $this->parse($code, $options);

        return $this->print($type);
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function tolerantParseAndPrint(#[Language('PHP')] string $code, array $options = []): string
    {
        $result = $this->partial($code, $options);

        self::assertInstanceOf(SuccessfulParsedResult::class, $result);

        return $this->print($result->type);
    }
}
