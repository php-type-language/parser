<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Parser\Partial\ParsedResult;
use TypeLang\Parser\Validation\CheckResult;
use TypeLang\Type\TypeNode;

/**
 * @template-contravariant TSource of mixed = mixed
 *
 * @template-implements TypeParserInterface<TSource>
 */
final class TypeParser implements TypeParserInterface
{
    private readonly SourceFactoryInterface $sources;

    private ?Executor $executor = null;

    public function __construct(
        public readonly TypeParserFeatures $features = new TypeParserFeatures(),
        ?SourceFactoryInterface $sources = null,
    ) {
        $this->sources = $sources ?? SourceFactory::createDefault();
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
    public function withFeatures(bool ...$features): self
    {
        return new self(
            features: $this->features->with(...$features),
            sources: $this->sources,
        );
    }

    /**
     * Reads the provided source code into the tokens it is written of,
     * building nothing out of them.
     *
     * The tokens are what every other method of this parser reads the source
     * through, so this is the source as the grammar sees it: what a token is
     * called, what it carries, and where it stands.
     *
     * ```
     * foreach ($parser->lex('array{ field: result }') as $token) {
     *     echo $token->name . ' ' . $token->value . \PHP_EOL;
     * }
     *
     * // => T_NAME array
     * // => T_BRACE_OPEN {
     * // => ...
     * ```
     *
     * @param TSource $source source code to read
     * @return iterable<array-key, TokenInterface> the tokens the source is
     *         written of
     * @throws \Throwable in case of internal error occurs
     */
    public function lex(#[Language('PHP')] mixed $source): iterable
    {
        $executor = $this->getExecutor();

        return $executor->lex($this->toSource($source));
    }

    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $executor = $this->getExecutor();

        return $executor->parse($this->toSource($source));
    }

    public function partial(#[Language('PHP')] mixed $source): ParsedResult
    {
        $executor = $this->getExecutor();

        return $executor->partial($this->toSource($source));
    }

    public function validate(#[Language('PHP')] mixed $source): CheckResult
    {
        $executor = $this->getExecutor();

        return $executor->validate($this->toSource($source));
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
     * Returns a lazily created parser recognizing a source with the features
     * of this one.
     */
    private function getExecutor(): Executor
    {
        return $this->executor ??= new Executor($this->features);
    }
}
