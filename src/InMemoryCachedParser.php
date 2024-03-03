<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\SourceExceptionInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class InMemoryCachedParser implements ParserInterface
{
    /**
     * @var array<non-empty-string, TypeStatement|null>
     */
    private array $types = [];

    public function __construct(
        private readonly ParserInterface $parser = new Parser(),
        private readonly SourceFactoryInterface $sources = new SourceFactory(),
    ) {}

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parse(#[Language('PHP')] mixed $source): ?TypeStatement
    {
        $instance = $this->sources->create($source);

        return $this->types[$instance->getHash()] ??= $this->parser->parse($source);
    }
}
