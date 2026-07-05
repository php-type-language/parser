<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use JetBrains\PhpStorm\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\ParsedResult;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * @phpstan-type ParserOptionsType array{
 *     tolerant?: bool,
 *     conditional?: bool,
 *     shapes?: bool,
 *     callables?: bool,
 *     literals?: bool,
 *     generics?: bool,
 *     union?: bool,
 *     intersection?: bool,
 *     list?: bool,
 *     offsets?: bool,
 *     hints?: bool,
 *     attributes?: bool,
 * }
 */
#[Group('unit'), Group('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
    protected TypeParserInterface $parser {
        get => $this->parser ??= new TypeParser();
    }

    /**
     * @param ParserOptionsType $options
     */
    protected function parser(array $options = []): TypeParserInterface
    {
        if ($options === []) {
            return $this->parser;
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
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parseTolerant(#[Language('PHP')] string $code, array $options = []): ParsedResult
    {
        $parser = $this->parser($options);

        return $parser->parseTolerant($code);
    }

    protected function print(TypeNode $statement): string
    {
        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$statement]);

        return \trim($visitor->getOutput());
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
        $result = $this->tolerant($code, $options);

        return $this->print($result->type);
    }
}
