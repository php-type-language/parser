<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Position\PositionFactoryInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\PartialResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Parser\Exception\ParserRuntimeException;
use Phplrt\Parser\Exception\UnexpectedTokenException as PhplrtUnexpectedTokenException;
use Phplrt\Position\PositionFactory;
use TypeLang\Parser\Exception\MalformedSyntaxException;
use TypeLang\Parser\Exception\SyntaxErrorException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
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
     * @throws SyntaxErrorException in case of the source is not a valid type
     * @throws \Throwable in case of the source cannot be read
     */
    public function parse(ReadableInterface $source): mixed
    {
        try {
            return parent::parse($source);
        } catch (ParserRuntimeException $e) {
            throw $this->translate($e);
        }
    }

    private function describe(\Throwable $e): string
    {
        if ($e instanceof ParserRuntimeException) {
            return $this->translate($e)
                ->getMessage();
        }

        return $e->getMessage();
    }

    /**
     * Returns the tokens the reading could continue with, nothing is expected
     * after a complete type.
     *
     * @return list<non-empty-string>
     */
    private function getExpected(ParserRuntimeException $e): array
    {
        if (!$e instanceof PhplrtUnexpectedTokenException) {
            return [];
        }

        $result = $this->parser->analyze($e->source, Mode::Tolerant);

        if ($result instanceof PartialResult && $result->token->offset === $e->token->offset) {
            return [];
        }

        return $e->expected;
    }

    /**
     * Tells the failure of the grammar in the exceptions of the library.
     */
    private function translate(ParserRuntimeException $e): SyntaxErrorException
    {
        $expected = $this->getExpected($e);

        return match (true) {
            // The code of an error is the number of the grammar rule that
            // describes it with a message of its own
            $e->getCode() > 0 => MalformedSyntaxException::becauseConstructIsMalformed(
                $e->source,
                $e->token,
                $e->getMessage(),
            ),
            $e->token->channel === Channel::Unknown => UnrecognizedSyntaxException::becauseFragmentIsNotRecognized(
                $e->source,
                $e->token,
            ),
            default => UnexpectedTokenException::becauseTokenIsUnexpected(
                $e->source,
                $e->token,
                $expected,
            ),
        };
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
                message: $this->describe($result->error),
                position: $this->positions->createFromOffset($source, $result->token->offset),
                offset: $result->token->offset,
            ),
            $result instanceof FailureResult => new FailureCheckResult(
                message: $this->describe($result->error),
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
                message: $this->describe($result->error),
                position: $this->positions->createFromOffset($source, $result->token->offset),
                offset: $result->token->offset,
            ),
        };
    }
}
