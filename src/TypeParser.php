<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceExceptionInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Internal\ExecutionContext;
use TypeLang\Type\TypeNode;

final class TypeParser implements TypeParserInterface
{
    private ExecutionContext $strict {
        get => $this->strict ??= new ExecutionContext($this->features, false);
    }

    private ExecutionContext $tolerant {
        get => $this->tolerant ??= new ExecutionContext($this->features, true);
    }

    public function __construct(
        public readonly TypeParserFeatures $features = new TypeParserFeatures(),
        private readonly SourceFactoryInterface $sources = new SourceFactory(),
    ) {}

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
    public function withFeatures(bool ...$features): self
    {
        return new self(
            features: $this->features->with(...$features),
            sources: $this->sources,
        );
    }

    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $result = $this->execute($this->strict, $source);

        return $result->type;
    }

    public function parseTolerant(#[Language('PHP')] mixed $source): ParsedResult
    {
        return $this->execute($this->tolerant, $source);
    }

    private function execute(ExecutionContext $context, mixed $source): ParsedResult
    {
        try {
            $instance = $this->sources->create($source);

            try {
                return $context->parse($instance) ?? throw new ParseException(
                    message: 'Could not read type statement',
                    code: ParseException::ERROR_CODE_INTERNAL_ERROR,
                );
            } catch (UnexpectedTokenException $e) {
                throw $this->unexpectedTokenError($e, $instance);
            } catch (UnrecognizedTokenException $e) {
                throw $this->unrecognizedTokenError($e, $instance);
            } catch (ParserRuntimeExceptionInterface $e) {
                throw $this->parserRuntimeError($e, $instance);
            } catch (SemanticException $e) {
                throw $this->semanticError($e, $instance);
            } catch (\Throwable $e) {
                throw $this->internalError($e, $instance);
            }
        } catch (SourceExceptionInterface $e) {
            throw new ParseException(
                message: $e->getMessage(),
                code: ParseException::ERROR_CODE_INTERNAL_ERROR,
                previous: $e,
            );
        }
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function unexpectedTokenError(UnexpectedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnexpectedToken(
            token: $token->getValue(),
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function unrecognizedTokenError(UnrecognizedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedToken(
            token: $token->getValue(),
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function semanticError(SemanticException $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromSemanticError($e, $source);
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function parserRuntimeError(ParserRuntimeExceptionInterface $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedSyntaxError(
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function internalError(\Throwable $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromInternalError(
            statement: $source->getContents(),
            e: $e,
        );
    }
}
