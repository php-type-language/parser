<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

interface NodeInterface
{
    /**
     * Returns token offset defined in the source code.
     *
     * It is recommended to use the `phplrt/position` package to determine
     * the line and column from this information:
     *
     * ```php
     * $position = Phplrt\Position\Position::fromOffset(
     *     source: \file_get_contents($filename),
     *     offset: $node->getOffset(),
     * );
     *
     * echo 'line: ' . $position->getLine() . "\n"
     *      'column: ' . $position->getColumn();
     * ```
     *
     * @return int<0, max>
     */
    public function getOffset(): int;
}
