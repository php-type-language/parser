<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class FeatureNotAllowedException extends SemanticException
{
    /**
     * Occurs when a syntax feature is used while disabled in the parser
     * configuration.
     *
     * @param non-empty-string $name
     * @param int<0, max> $offset
     */
    public static function becauseFeatureIsNotAllowed(
        string $name,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf('%s not allowed', \ucfirst($name)),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
