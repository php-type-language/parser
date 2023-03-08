<?php

declare(strict_types=1);

namespace Hyper\Bridge;

/**
 * @template TOutLiteral
 */
interface LiteralsProviderInterface
{
    /**
     * Returns a type describing the passed {@see bool} literal value.
     *
     * @return TOutLiteral
     */
    public function bool(bool $literal): mixed;

    /**
     * Returns a type describing {@see null} literal value.
     *
     * @return TOutLiteral
     */
    public function null(): mixed;

    /**
     * Returns a type describing the passed {@see string} literal value.
     *
     * @return TOutLiteral
     */
    public function string(string $literal): mixed;

    /**
     * Returns a type describing the passed {@see float} literal value.
     *
     * @return TOutLiteral
     */
    public function float(float $literal): mixed;

    /**
     * Returns a type describing the passed {@see int} literal value.
     *
     * @return TOutLiteral
     */
    public function int(int $literal): mixed;

    /**
     * Returns a reference to the class
     *
     * @param class-string $fqn
     *
     * @return TOutLiteral
     */
    public function reference(string $fqn): mixed;

    /**
     * @param array<array-key, TOutLiteral> $fields
     *
     * @return TOutLiteral
     */
    public function shape(array $fields, bool $sealed): object;
}
