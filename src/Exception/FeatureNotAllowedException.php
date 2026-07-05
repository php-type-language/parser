<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class FeatureNotAllowedException extends SemanticException
{
    /**
     * Occurs when a syntax feature is used while disabled in the parser
     * configuration.
     *
     * @param non-empty-string $name
     * @param int<0, max> $offset
     */
    public static function becauseFeatureIsNotAllowed(string $name, int $offset = 0): self
    {
        $message = \sprintf('%s not allowed', \ucfirst($name));

        return new self($offset, $message);
    }
}
