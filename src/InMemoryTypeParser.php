<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Partial\ParsedResult;
use TypeLang\Parser\Validation\CheckResult;
use TypeLang\Type\TypeNode;

final class InMemoryTypeParser implements TypeParserInterface
{
    /**
     * @var non-empty-string
     */
    private const HASH_ALGORITHM = 'xxh128';

    /**
     * @var array<non-empty-string, TypeNode>
     */
    private array $types = [];

    /**
     * @var array<non-empty-string, ParsedResult>
     */
    private array $sequences = [];

    /**
     * @var array<non-empty-string, CheckResult>
     */
    private array $checks = [];

    private readonly SourceFactoryInterface $sources;

    public function __construct(
        private readonly TypeParserInterface $parser = new TypeParser(),
        ?SourceFactoryInterface $sources = null,
    ) {
        $this->sources = $sources ?? SourceFactory::createDefault();
    }

    public function reset(): void
    {
        $this->types = [];
        $this->sequences = [];
        $this->checks = [];
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $instance = $this->sources->create($source);

        return $this->types[$this->hash($instance)] ??= $this->parser->parse($source);
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function partial(#[Language('PHP')] mixed $source): ParsedResult
    {
        $instance = $this->sources->create($source);

        return $this->sequences[$this->hash($instance)] ??= $this->parser->partial($source);
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function validate(#[Language('PHP')] mixed $source): CheckResult
    {
        $instance = $this->sources->create($source);

        return $this->checks[$this->hash($instance)] ??= $this->parser->validate($source);
    }

    /**
     * @return non-empty-string
     */
    private function hash(ReadableInterface $source): string
    {
        return \hash(self::HASH_ALGORITHM, $source->content);
    }
}
