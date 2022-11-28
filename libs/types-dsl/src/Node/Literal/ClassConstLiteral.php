<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;
use Hyper\Type\DSL\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class ClassConstLiteral extends Literal
{
    /**
     * @param int $offset
     * @param Name $class
     * @param string $name
     * @param mixed $value
     */
    public function __construct(
        int $offset,
        public readonly Name $class,
        public readonly string $name,
        public readonly mixed $value
    ) {
        parent::__construct($offset);
    }

    /**
     * @param Name $class
     * @param TokenInterface $value
     * @return $this
     */
    public static function parse(Name $class, TokenInterface $value): self
    {
        $const = $value->getValue();

        if (\is_subclass_of($class->name, \BackedEnum::class, true)
            || \is_subclass_of($class->name, \UnitEnum::class, true)) {
            foreach (($class->name)::cases() as $case) {
                if ($case->name === $const) {
                    return new self($class->offset, $class, $const, $case);
                }
            }

            throw new \InvalidArgumentException(
                \sprintf('The case "%s" is not a valid case of enum "%s"', $const, $class->name)
            );
        }

        return new self($class->offset, $class, $const, \constant($class->name . '::' . $const));
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
