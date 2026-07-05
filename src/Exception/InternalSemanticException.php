<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class InternalSemanticException extends SemanticException
{
    /**
     * Occurs when an unexpected sub-node is encountered while building a
     * square bracket type and signals a bug in the parser itself.
     *
     * @param int<0, max> $offset
     */
    public static function becauseSubNodeIsUnexpected(string $type, int $offset = 0): self
    {
        $message = \sprintf('Internal error, unexpected square bracket sub-node %s', $type);

        return new self($offset, $message);
    }
}
