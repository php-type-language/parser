<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class FeatureNotAllowedException extends SemanticException
{
    /**
     * @param non-empty-string $name
     * @param int<0, max> $offset
     */
    public static function fromFeature(string $name, int $offset = 0): self
    {
        $message = \sprintf('%s not allowed by parser configuration', $name);

        return new self($message, $offset);
    }
}
