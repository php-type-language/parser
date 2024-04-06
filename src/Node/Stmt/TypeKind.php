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
     */
    case UNKNOWN = Kind::TYPE_KIND;

    /**
     * Denotes any named type, which may be a reference to a class,
     * interface, enum, trait, scalar, or some other type.
     *
     * ```
     * Path\To\SomeClass<T, Y>
     * ```
     */
    case TYPE_KIND = Kind::TYPE_KIND + 1;

    /**
     * Denotes any callable type that can contain parameters
     * and a return type definition.
     *
     * ```
     * example(T, U): V
     * ```
     */
    case CALLABLE_KIND = Kind::TYPE_KIND + 2;

    /**
     * Denotes a constant mask for a class.
     *
     * ```
     * Path\To\SomeClass::CONST_*
     * ```
     */
    case CLASS_CONST_MASK_KIND = Kind::TYPE_KIND + 3;

    /**
     * Denotes a reference to a specific class constant.
     *
     * ```
     * Path\To\SomeClass::CONST_NAME
     * ```
     */
    case CLASS_CONST_KIND = Kind::TYPE_KIND + 4;

    /**
     * Denotes a global constant mask.
     *
     * ```
     * JSON_ERROR_*
     * ```
     */
    case CONST_MASK_KIND = Kind::TYPE_KIND + 5;

    /**
     * Denotes the logical intersection type.
     *
     * ```
     * T & U
     * ```
     */
    case LOGICAL_INTERSECTION_KIND = Kind::TYPE_KIND + 6;

    /**
     * Denotes the logical union type.
     *
     * ```
     * T | U
     * ```
     */
    case LOGICAL_UNION_KIND = Kind::TYPE_KIND + 7;

    /**
     * Denotes the nullable type.
     *
     * ```
     * ?T
     * ```
     */
    case NULLABLE_KIND = Kind::TYPE_KIND + 8;

    /**
     * Denotes the logical ternary type.
     *
     * ```
     * T ? U : V
     * ```
     */
    case TERNARY_KIND = Kind::TYPE_KIND + 9;

    /**
     * Indicates a list type in the "legacy" syntax format.
     *
     * ```
     * T[]
     * ```
     */
    case LIST_KIND = Kind::TYPE_KIND + 10;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
