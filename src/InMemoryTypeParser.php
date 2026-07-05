<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\SourceExceptionInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Type\TypeNode;

final class InMemoryTypeParser implements TypeParserInterface
{
    /**
     * @var array<non-empty-string, TypeNode>
     */
    private array $types = [];

    /**
     * @var array<non-empty-string, ParsedResult>
     */
    private array $sequences = [];

    public function __construct(
        private readonly TypeParserInterface $parser = new TypeParser(),
        private readonly SourceFactoryInterface $sources = new SourceFactory(),
    ) {}

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $instance = $this->sources->create($source);

        return $this->types[$instance->getHash()] ??= $this->parser->parse($source);
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parseTolerant(#[Language('PHP')] mixed $source): ParsedResult
    {
        $instance = $this->sources->create($source);

        return $this->sequences[$instance->getHash()] ??= $this->parser->parseTolerant($source);
    }
}
