<?php

declare(strict_types=1);

namespace Hyper\Type\Factory;

use Hyper\Bridge\InstantiatorInterface;
use Hyper\Type\AliasInterface;
use Hyper\Type\Attribute\Usage;
use Hyper\Type\LiteralTypeInterface;
use Hyper\Type\TypeInterface;

/**
 * @template TOut of TypeInterface
 * @template-implements InstantiatorInterface<TOut>
 */
final class Instantiator implements InstantiatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function new(string $type, array $params = []): object
    {
        if ($params === []) {
            return new $type();
        }

        try {
            return new $type(...$this->unwrap($params));
        } catch (\TypeError $e) {
            $message = \sprintf('Cannot create type %s because invalid parameters were passed.', $type);
            $samples = $this->samples(new \ReflectionClass($type));
            if ($samples !== []) {
                $message .= ' Make sure that the parameters are passed in one of the allowed formats:';
                $message .= ' ' . \implode(', ', $samples);
            }

            throw new \InvalidArgumentException($message, (int)$e->getCode(), $e);
        }
    }

    private function samples(\ReflectionClass $class): array
    {
        $result = [];

        /** @var  $usage */
        foreach ($class->getAttributes(Usage::class) as $usage) {
            /** @var Usage $instance */
            $instance = $usage->newInstance();

            $result[] = $class->getShortName() . $instance->sample;
        }

        return $result;
    }

    private function unwrap(iterable $params): array
    {
        $result = [];

        foreach ($params as $name => $param) {
            // Going down the chain of aliases
            while ($param instanceof AliasInterface) {
                $param = $param->getType();
            }

            // Unwrap literals
            // if ($param instanceof LiteralTypeInterface) {
            //     $param = $param->getValue();
            // }

            $result[$name] = $param;
        }

        return $result;
    }
}
