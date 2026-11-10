<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Context;
use TypeLang\Parser\TypeParserFeatures;

/**
 * The source uses a syntax of the feature ({@see TypeParserFeatures}) that the
 * parser has disabled.
 *
 * ```
 *  $parser = new TypeParser(new TypeParserFeatures(literals: false));
 *  $parser->parse('42');
 *  // => FeatureNotEnabledException: Literals not allowed
 * ```
 */
final class FeatureNotEnabledException extends ParsingException
{
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     * @param non-empty-string $feature The name of the disabled feature
     */
    public static function becauseFeatureIsNotEnabled(
        string $feature,
        ReadableInterface $source,
        int $offset = 0,
        int $length = 0,
    ): self {
        $message = \sprintf('%s feature is not allowed', \ucfirst($feature));

        return new self(new ParsingErrorRange($offset, $length), $source, $message);
    }

    /**
     * @param non-empty-string $feature
     */
    public static function becauseFeatureIsNotEnabledIn(string $feature, Context $ctx): self
    {
        return self::becauseFeatureIsNotEnabled($feature, $ctx->source, $ctx->begin, $ctx->length);
    }
}
