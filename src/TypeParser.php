<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Position\PositionFactoryInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Position\PositionFactory;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\Validation\FailureCheckResult;
use TypeLang\Parser\Validation\SuccessfulCheckResult;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\TypeNode;

/**
 * @template-covariant TResult of Node = TypeNode
 * @template-contravariant TSource of mixed = mixed
 *
 * @template-implements TypeParserInterface<TResult, TSource>
 */
final class TypeParser implements TypeParserInterface
{
    private readonly SourceFactoryInterface $sources;
    private readonly PositionFactoryInterface $positions;

    private ?Executor $executor = null;

    public function __construct(
        public readonly TypeParserFeatures $features = new TypeParserFeatures(),
        ?SourceFactoryInterface $sources = null,
    ) {
        $this->sources = $sources ?? SourceFactory::createDefault();
        $this->positions = new PositionFactory();
    }

    /**
     * Returns a new parser with an overridden parser feature flag.
     *
     * ```
     * $parser = $parser->withFeatures(
     *     conditions: true,
     *     hints: false,
     * );
     * ```
     */
    public function with(bool ...$features): self
    {
        return new self(
            features: $this->features->with(...$features),
            sources: $this->sources,
        );
    }

    public function parse(#[Language('PHP')] mixed $source): Node
    {
        /** @var Executor<TResult> $executor */
        $executor = $this->getExecutor();

        return $executor->parse($this->toSource($source));
    }

    public function partial(#[Language('PHP')] mixed $source): SuccessfulParsedResult|FailureParsedResult
    {
        /** @var Executor<TResult> $executor */
        $executor = $this->getExecutor();

        return $executor->partial($this->toSource($source));
    }

    public function validate(#[Language('PHP')] mixed $source): SuccessfulCheckResult|FailureCheckResult
    {
        /** @var Executor<TResult> $executor */
        $executor = $this->getExecutor();

        return $executor->validate($this->toSource($source));
    }

    private function getExecutor(): Executor
    {
        return $this->executor ??= new Executor(
            features: $this->features,
            positions: $this->positions,
        );
    }

    /**
     * @throws SourceExceptionInterface in case of no source can be created out
     *         of the given value
     */
    private function toSource(mixed $source): ReadableInterface
    {
        return $this->sources->create($source);
    }

    /**
     * Returns type recognition context:
     * ```
     * $result = $parser->forType()
     *     ->parse('Type<U, V>');
     * ```
     *
     * @api
     *
     * @return self<TypeNode, TSource>
     */
    public function forType(): self
    {
        /** @var self<TypeNode, TSource> */
        return $this->for(Internal\CompiledExecutor::Type);
    }

    /**
     * Returns template parameter recognition context:
     * ```
     * $result = $parser->forType()
     *     ->parse('T of U<V> = Default<Z>');
     * ```
     *
     * @api
     *
     * @return self<TemplateParameterNode, TSource>
     */
    public function forTemplateParameter(): self
    {
        /** @var self<TemplateParameterNode, TSource> */
        return $this->for(Internal\CompiledExecutor::TemplateParameter);
    }

    /**
     * @param int<0, max> $rule
     * @return self<Node, TSource>
     */
    private function for(int $rule): self
    {
        $executor = $this->getExecutor();

        $self = clone $this;
        /** @phpstan-ignore-next-line : Allow executor type change */
        $self->executor = $executor->for($rule);

        return $self;
    }
}
