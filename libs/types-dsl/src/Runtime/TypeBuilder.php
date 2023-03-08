<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Runtime;

use Hyper\Type\BoolLiteral;
use Hyper\Type\DSL\Node\Argument;
use Hyper\Type\DSL\Node\NamedArgument;
use Hyper\Type\DSL\Node\Stmt\Literal\BoolLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\Literal\ClassConstLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\Literal\FloatLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\Literal\IntLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\Literal\LiteralStatement;
use Hyper\Type\DSL\Node\Stmt\Literal\NullLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\Literal\StringLiteralStmt;
use Hyper\Type\DSL\Node\Stmt\NullableTypeStmt;
use Hyper\Type\DSL\Node\Stmt\TypeStmt;
use Hyper\Type\FalseLiteral;
use Hyper\Type\FloatLiteral;
use Hyper\Type\IntLiteral;
use Hyper\Type\Nullable;
use Hyper\Type\NullLiteral;
use Hyper\Type\Repository\RepositoryInterface;
use Hyper\Type\StringLiteral;
use Hyper\Type\TrueLiteral;
use Hyper\Type\TypeInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
final class TypeBuilder
{
    public function __construct(
        private readonly RepositoryInterface $types,
    ) {
    }

    /**
     * @param TypeStmt $type
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

        if ($type instanceof NullableTypeStmt) {
            return $this->nullable($result);
        }

        return $result;
    }

    private function nullable(TypeInterface $type): Nullable
    {
        return new Nullable($type);
    }

    private function string(StringLiteralStmt $node): StringLiteral
    {
        return new StringLiteral($node->getValue());
    }

    private function int(IntLiteralStmt $node): IntLiteral
    {
        return new IntLiteral($node->getValue());
    }

    private function float(FloatLiteralStmt $node): FloatLiteral
    {
        return new FloatLiteral($node->getValue());
    }

    private function bool(BoolLiteralStmt $node): BoolLiteral
    {
        if ($node->getValue()) {
            return new TrueLiteral();
        }

        return new FalseLiteral();
    }

    private function null(NullLiteralStmt $node): NullLiteral
    {
        return new NullLiteral();
    }

    /**
     * @param Argument $argument
     *
     */
    private function getArgumentValue(Argument $argument): mixed
    {
        $value = $argument->value;

        if ($value instanceof LiteralStatement) {
            return match (true) {
                $value instanceof IntLiteralStmt => $this->int($value),
                $value instanceof BoolLiteralStmt => $this->bool($value),
                $value instanceof NullLiteralStmt => $this->null($value),
                $value instanceof StringLiteralStmt => $this->string($value),
                $value instanceof FloatLiteralStmt => $this->float($value),
                $value instanceof ClassConstLiteralStmt => $this->make($value->getValue()),
            };
        }

        if ($this->isNonTypeClass($value)) {
            return $value->name->name;
        }

        return $this->make($value);
    }

    /**
     * @param TypeStmt $stmt
     */
    private function isNonTypeClass(TypeStmt $stmt): bool
    {
        return $stmt->args === []
            && \class_exists($stmt->name->name)
            && !\is_a($stmt->name->name, TypeInterface::class, true);
    }
}
