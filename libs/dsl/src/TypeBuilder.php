<?php

declare(strict_types=1);

namespace Hyper\DSL;

use Hyper\Bridge\FactoryInterface;
use Hyper\Parser\Node\Literal\BoolLiteralStmt;
use Hyper\Parser\Node\Literal\FloatLiteralStmt;
use Hyper\Parser\Node\Literal\IntLiteralStmt;
use Hyper\Parser\Node\Literal\NullLiteralStmt;
use Hyper\Parser\Node\Literal\StringLiteralStmt;
use Hyper\Parser\Node\Stmt\IntersectionTypeStmt;
use Hyper\Parser\Node\Stmt\NamedTypeStmt;
use Hyper\Parser\Node\Stmt\NullableTypeStmt;
use Hyper\Parser\Node\Stmt\Statement;
use Hyper\Parser\Node\Stmt\UnionTypeStmt;
use Hyper\Repository\Repository as TypesRepository;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\DSL
 */
final class TypeBuilder
{
    public function __construct(
        private readonly TypesRepository $types,
        private readonly FactoryInterface $bridge,
    ) {
    }

    public function make(Statement $stmt): object
    {
        return match(true) {
            $stmt instanceof BoolLiteralStmt => $this->bridge->bool($stmt->value),
            $stmt instanceof FloatLiteralStmt => $this->bridge->float($stmt->value),
            $stmt instanceof IntLiteralStmt => $this->bridge->int($stmt->value),
            $stmt instanceof NullLiteralStmt => $this->bridge->null(),
            $stmt instanceof StringLiteralStmt => $this->bridge->string($stmt->value),
            $stmt instanceof NullableTypeStmt => $this->bridge->maybe(
                $this->make($stmt->type),
            ),
            $stmt instanceof UnionTypeStmt => $this->bridge->or(
                $this->make($stmt->a),
                $this->make($stmt->b),
            ),
            $stmt instanceof IntersectionTypeStmt => $this->bridge->and(
                $this->make($stmt->a),
                $this->make($stmt->b),
            ),
            $stmt instanceof NamedTypeStmt => $this->type($stmt),
            default => throw new \InvalidArgumentException(
                'Unprocessable Node<' . $stmt::class . '>'
            ),
        };
    }

    private function type(NamedTypeStmt $stmt): object
    {
        // Template parameters and Arguments arguments
        $parameters = $arguments = [];

        if ($stmt->parameters !== null) {
            foreach ($stmt->parameters->list as $parameter) {
                $parameters[] = $this->make($parameter->value);
            }
        }

        if ($stmt->arguments !== null) {
            foreach ($stmt->arguments->list as $argument) {
                if ($argument->name !== null) {
                    $arguments[$argument->name] = $this->make($argument->value);
                } else {
                    $arguments[] = $this->make($argument->value);
                }
            }

            $parameters[] = $this->bridge->shape(
                $arguments,
                $stmt->arguments->sealed,
            );
        }

        return $this->types->get($stmt->name->name, $parameters);
    }
}
