<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Bridge\FactoryInterface;
use Hyper\Bridge\InstantiatorInterface;
use Hyper\Type\Complex\Struct;
use Hyper\Type\Factory\Instantiator;
use Hyper\Type\Literal\BoolLiteral;
use Hyper\Type\Literal\ClassReferenceLiteral;
use Hyper\Type\Literal\FalseLiteral;
use Hyper\Type\Literal\FloatLiteral;
use Hyper\Type\Literal\IntLiteral;
use Hyper\Type\Literal\NullLiteral;
use Hyper\Type\Literal\StringLiteral;
use Hyper\Type\Literal\TrueLiteral;
use Hyper\Type\Variadic\IntersectionType;
use Hyper\Type\Variadic\UnionType;

/**
 * @template-implements FactoryInterface<LiteralTypeInterface, TypeInterface, VariadicTypeInterface, GenericTypeInterface>
 */
final class Factory implements FactoryInterface
{
    private readonly InstantiatorInterface $instantiator;

    public function __construct()
    {
        $this->instantiator = new Instantiator();
    }

    public function new(string $type, array $params = []): TypeInterface
    {
        return $this->instantiator->new($type, $params);
    }

    public function shape(array $fields, bool $sealed): Struct
    {
        return new Struct($fields, $sealed);
    }

    public function maybe(object $type): UnionType
    {
        assert($type instanceof TypeInterface,
            $type::class . ' instanceof ' . TypeInterface::class);

        return $this->or(NullLiteral::getInstance(), $type);
    }

    public function or(object $a, object $b, object ...$other): UnionType
    {
        assert($a instanceof TypeInterface,
            $a::class . ' instanceof ' . TypeInterface::class);
        assert($b instanceof TypeInterface,
            $b::class . ' instanceof ' . TypeInterface::class);

        return new UnionType($a, $b, ...$other);
    }

    public function and(object $a, object $b, object ...$other): IntersectionType
    {
        assert($a instanceof TypeInterface,
            $a::class . ' instanceof ' . TypeInterface::class);
        assert($b instanceof TypeInterface,
            $b::class . ' instanceof ' . TypeInterface::class);

        return new IntersectionType($a, $b, ...$other);
    }

    public function bool(bool $literal): BoolLiteral
    {
        if ($literal) {
            return TrueLiteral::getInstance();
        }

        return FalseLiteral::getInstance();
    }

    public function null(): NullLiteral
    {
        return NullLiteral::getInstance();
    }

    public function string(string $literal): StringLiteral
    {
        return new StringLiteral($literal);
    }

    public function float(float $literal): FloatLiteral
    {
        return new FloatLiteral($literal);
    }

    public function int(int $literal): IntLiteral
    {
        return new IntLiteral($literal);
    }

    public function reference(string $fqn): ClassReferenceLiteral
    {
        return new ClassReferenceLiteral($fqn);
    }
}
