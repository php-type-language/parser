<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Snapshot;

use TypeLang\Type\Node;

/**
 * Writes a tree down to a single line with everything a node is read by:
 * the class, the offset and every public property of it.
 *
 * ```
 * NamedTypeNode@0(name: Name@0(value: "int", namespace: null), ...)
 * ```
 */
final class NodeDumper
{
    /**
     * The namespace that is left out of the class names.
     */
    private const NODE_NAMESPACE = 'TypeLang\\Type\\';

    /**
     * The characters of a string written down as escape sequences.
     */
    private const ESCAPED_CHARS = "\0..\37\\\"\177..\377";

    /**
     * @return non-empty-string
     */
    public function dump(mixed $value): string
    {
        /** @var non-empty-string */
        return match (true) {
            $value instanceof Node => $this->dumpNode($value),
            $value instanceof \UnitEnum => self::getShortName($value::class) . '::' . $value->name,
            \is_array($value) => '[' . \implode(', ', \array_map($this->dump(...), $value)) . ']',
            \is_string($value) => '"' . \addcslashes($value, self::ESCAPED_CHARS) . '"',
            $value === null => 'null',
            \is_bool($value) => $value ? 'true' : 'false',
            \is_int($value), \is_float($value) => \var_export($value, true),
            default => throw new \InvalidArgumentException(\sprintf(
                'A value of type %s is not expected in a tree',
                \get_debug_type($value),
            )),
        };
    }

    /**
     * @return non-empty-string
     */
    private function dumpNode(Node $node): string
    {
        $properties = [];

        foreach (\get_object_vars($node) as $name => $value) {
            if ($name === 'offset') {
                continue;
            }

            $properties[] = $name . ': ' . $this->dump($value);
        }

        return \sprintf(
            '%s@%d(%s)',
            self::getShortName($node::class),
            $node->offset,
            \implode(', ', $properties),
        );
    }

    /**
     * @param class-string $class
     * @return non-empty-string
     */
    private static function getShortName(string $class): string
    {
        if (\str_starts_with($class, self::NODE_NAMESPACE)) {
            /** @var non-empty-string */
            return \substr($class, \strlen(self::NODE_NAMESPACE));
        }

        return $class;
    }
}
