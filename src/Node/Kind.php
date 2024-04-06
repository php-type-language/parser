<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library interface, please do not use it in your code.
 * @psalm-internal TypeLang\Parser\Node
 */
interface Kind
{
    /**
     * Indicates an arbitrary and non-standard "kind"
     * category that has not been explicitly defined.
     */
    public const UNKNOWN = 0x0000;

    /**
     * Represents the initial value of any elements
     * that are of an arbitrary type.
     */
    public const TYPE_KIND = 0x0100;

    /**
     * Represents the initial value of any elements
     * representing the shape field type.
     */
    public const SHAPE_FIELD_KIND = 0x0200;

    /**
     * Represents the initial value of any elements
     * representing the conditional expression type.
     */
    public const CONDITION_KIND = 0x0300;

    /**
     * Represents the initial value of any elements
     * representing the literal value type.
     */
    public const LITERAL_KIND = 0x0400;
}
