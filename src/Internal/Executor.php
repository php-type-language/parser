<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Position\PositionFactoryInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\PartialResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Position\PositionFactory;
use TypeLang\Parser\Internal\SubParser\BinIntParser;
use TypeLang\Parser\Internal\SubParser\DecIntParser;
use TypeLang\Parser\Internal\SubParser\DqStringParser;
use TypeLang\Parser\Internal\SubParser\FloatParser;
use TypeLang\Parser\Internal\SubParser\HexIntParser;
use TypeLang\Parser\Internal\SubParser\OctIntParser;
use TypeLang\Parser\Internal\SubParser\OctLegacyIntParser;
use TypeLang\Parser\Internal\SubParser\SqStringParser;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Parser\Validation\FailureCheckResult;
use TypeLang\Parser\Validation\PartialCheckResult;
use TypeLang\Parser\Validation\SuccessfulCheckResult;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * @template T of Node = TypeNode
 *
 * @template-extends CompiledExecutor<T>
 *
 * @see TypeNode
 */
final class Executor extends CompiledExecutor
{
    protected readonly DecIntParser $decInt;
    protected readonly HexIntParser $hexInt;
    protected readonly BinIntParser $binInt;
    protected readonly OctIntParser $octInt;
    protected readonly OctLegacyIntParser $octLegacyInt;
    protected readonly FloatParser $float;
    protected readonly DqStringParser $dqString;
    protected readonly SqStringParser $sqString;

    public function __construct(
        protected readonly TypeParserFeatures $features,
        private readonly PositionFactoryInterface $positions = new PositionFactory(),
    ) {
        parent::__construct();

        $converter = BaseConverterFactory::default()
            ->select();

        $this->decInt = new DecIntParser($converter);
        $this->hexInt = new HexIntParser($converter);
        $this->binInt = new BinIntParser($converter);
        $this->octInt = new OctIntParser($converter);
        $this->octLegacyInt = new OctLegacyIntParser($converter);
        $this->float = new FloatParser();
        $this->dqString = new DqStringParser();
        $this->sqString = new SqStringParser();
    }

    /**
     * @param int<0, max> $rule
     * @return self<Node>
     */
    public function for(int $rule): self
    {
        $self = clone $this;
        $self->parser = $self->parser->withInitial($rule);

        return $self;
    }

    /**
     * Reads as much of the source as the grammar describes, a source that is
     * no type is not an error but a failed result.
     *
     * @throws \Throwable in case of the source cannot be read
     */
    public function validate(ReadableInterface $source): SuccessfulCheckResult|FailureCheckResult
    {
        $result = $this->parser->analyze($source, Mode::SyntaxCheck);

        return match (true) {
            $result instanceof PartialResult => new PartialCheckResult(
                message: $result->error->getMessage(),
                position: $this->positions->createFromOffset($source, $result->token->offset),
                offset: $result->token->offset,
            ),
            $result instanceof FailureResult => new FailureCheckResult(
                message: $result->error->getMessage(),
                position: $this->positions->createFromOffset($source, $result->token->offset),
                offset: $result->token->offset,
            ),
            default => new SuccessfulCheckResult(),
        };
    }

    /**
     * Reads as much of the source as the grammar describes, a source that is
     * no type is not an error but a failed result.
     *
     * @return SuccessfulParsedResult<T>|FailureParsedResult
     * @throws \Throwable in case of the source cannot be read
     */
    public function partial(ReadableInterface $source): SuccessfulParsedResult|FailureParsedResult
    {
        $result = $this->parser->analyze($source, Mode::Tolerant);

        return match (true) {
            $result instanceof PartialResult => new PartialParsedResult(
                /** @phpstan-ignore-next-line : PHPStan "unresolvable type" bug */
                $result->value,
                $result->token->offset,
            ),
            $result instanceof SuccessfulResult => new SuccessfulParsedResult(
                $result->value,
                \strlen($source->content),
            ),
            default => new FailureParsedResult(
                message: $result->error->getMessage(),
                position: $this->positions->createFromOffset($source, $result->token->offset),
                offset: $result->token->offset,
            ),
        };
    }
}
