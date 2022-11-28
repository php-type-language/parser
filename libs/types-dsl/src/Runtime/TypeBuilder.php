<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Runtime;

use Hyper\Type\DSL\Node\Argument;
use Hyper\Type\DSL\Node\Literal\Literal;
use Hyper\Type\DSL\Node\NamedArgument;
use Hyper\Type\DSL\Node\Stmt\NullableTypeStmt;
use Hyper\Type\DSL\Node\Stmt\TypeStmt;
use Hyper\Type\NullableType;
use Hyper\Type\Repository\RepositoryInterface;
use Hyper\Type\TypeInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
final class TypeBuilder
{
    /**
     * @param RepositoryInterface $types
     */
    public function __construct(
        private readonly RepositoryInterface $types,
    ) {
    }

    /**
     * @param TypeStmt $type
     * @return TypeInterface
     */
    public function make(TypeStmt $type): TypeInterface
    {
        $args = [];

        foreach ($type->args as $argument) {
            if ($argument instanceof Argument) {
                $args[] = $this->getArgumentValue($argument);
            } elseif($argument instanceof NamedArgument) {
                $args[$argument->name] = $this->getArgumentValue($argument->argument);
            }
        }

        $result = $this->types->get($type->name->name, $args);

        return $this->getConcreteType($type, $result);
    }

    /**
     * @template TType of TypeInterface
     *
     * @param TypeStmt $node
     * @param TType $type
     *
     * @return TType|TypeInterface<TType>
     */
    private function getConcreteType(TypeStmt $node, TypeInterface $type): TypeInterface
    {
        if ($node instanceof NullableTypeStmt) {
            return new NullableType($type);
        }

        return $type;
    }

    /**
     * @param Argument $argument
     *
     * @return mixed
     */
    private function getArgumentValue(Argument $argument): mixed
    {
        $value = $argument->value;

        if ($value instanceof Literal) {
            return $value->getValue();
        }

        if ($this->isNonTypeClass($value)) {
            return $value->name->name;
        }

        return $this->make($value);
    }

    /**
     * @param TypeStmt $stmt
     * @return bool
     */
    private function isNonTypeClass(TypeStmt $stmt): bool
    {
        return $stmt->args === []
            && \class_exists($stmt->name->name)
            && !\is_a($stmt->name->name, TypeInterface::class, true);
    }
}
