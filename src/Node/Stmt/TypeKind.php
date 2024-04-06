<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Kind;

enum TypeKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard type
     * that has not been explicitly defined.
     *
     * @internal
     * @see Kind::TYPE_KIND
     */
    case UNKNOWN = /*Kind::TYPE_KIND*/0x0100;

    /**
     * Denotes any named type, which may be a reference to a class,
     * interface, enum, trait, scalar, or some other type.
     *
     * ```
     * Path\To\SomeClass<T, Y>
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case TYPE_KIND = /*Kind::TYPE_KIND*/0x0100 + 1;

    /**
     * Denotes any callable type that can contain parameters
     * and a return type definition.
     *
     * ```
     * example(T, U): V
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case CALLABLE_KIND = /*Kind::TYPE_KIND*/0x0100 + 2;

    /**
     * Denotes a constant mask for a class.
     *
     * ```
     * Path\To\SomeClass::CONST_*
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case CLASS_CONST_MASK_KIND = /*Kind::TYPE_KIND*/0x0100 + 3;

    /**
     * Denotes a reference to a specific class constant.
     *
     * ```
     * Path\To\SomeClass::CONST_NAME
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case CLASS_CONST_KIND = /*Kind::TYPE_KIND*/0x0100 + 4;

    /**
     * Denotes a global constant mask.
     *
     * ```
     * JSON_ERROR_*
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case CONST_MASK_KIND = /*Kind::TYPE_KIND*/0x0100 + 5;

    /**
     * Denotes the logical intersection type.
     *
     * ```
     * T & U
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case LOGICAL_INTERSECTION_KIND = /*Kind::TYPE_KIND*/0x0100 + 6;

    /**
     * Denotes the logical union type.
     *
     * ```
     * T | U
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case LOGICAL_UNION_KIND = /*Kind::TYPE_KIND*/0x0100 + 7;

    /**
     * Denotes the nullable type.
     *
     * ```
     * ?T
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case NULLABLE_KIND = /*Kind::TYPE_KIND*/0x0100 + 8;

    /**
     * Denotes the logical ternary type.
     *
     * ```
     * T ? U : V
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case TERNARY_KIND = /*Kind::TYPE_KIND*/0x0100 + 9;

    /**
     * Indicates a list type in the "legacy" syntax format.
     *
     * ```
     * T[]
     * ```
     *
     * @see Kind::TYPE_KIND
     */
    case LIST_KIND = /*Kind::TYPE_KIND*/0x0100 + 10;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
