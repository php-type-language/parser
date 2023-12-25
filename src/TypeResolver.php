<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\FullQualifiedName;
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\Traverser\TypeMapVisitor;

final class TypeResolver implements TypeResolverInterface
{
    public function resolve(TypeStatement $type, callable $transform): TypeStatement
    {
        Traverser::through(
            visitor: new TypeMapVisitor($transform(...)),
            nodes: [$type],
        );

        return $type;
    }

    /**
     * Use for example for code like this:
     * ```php
     *  use TypeLang\Parser\Node;
     *  use TypeLang\Parser\Exception as Error;
     *
     *  $parser = new TypeLang\Parser\Parser();
     *  $result = $parser->parse(<<<'PHP'
     *      array { Node, Error\SemanticException }
     *      PHP);
     *
     *  $resolver = new \TypeLang\Parser\TypeResolver();
     *  $result = $resolver->resolveWith($expected, [
     *      'TypeLang\Parser\Node',                     // use TypeLang\Parser\Node;
     *      'Error' => 'TypeLang\Parser\Exception',     // use TypeLang\Parser\Exception as Error;
     *  ]);
     *
     *  // Expected Output:
     *  // > array{
     *  // >     TypeLang\Parser\Node,
     *  // >     TypeLang\Parser\Exception\SemanticException
     *  // > }
     * ```
     *
     * @param array<non-empty-string|array-key, non-empty-string|Name> $replacements
     */
    public function resolveWith(TypeStatement $type, array $replacements): TypeStatement
    {
        foreach ($replacements as $key => $replacement) {
            // normalize value
            if (\is_string($replacement)) {
                $replacement = \str_starts_with($replacement, '\\')
                    ? new FullQualifiedName($replacement)
                    : new Name($replacement);
            }

            // normalize key
            if (\is_int($key)) {
                unset($replacements[$key]);

                $key = $replacement->getLastPartAsString();
            }

            $replacements[\strtolower($key)] = $replacement;
        }

        /** @var array<non-empty-lowercase-string, Name> $replacements */
        return $this->resolve($type, static function (Name $name) use ($replacements) {
            $first = \strtolower($name->getFirstPartAsString());

            if (isset($replacements[$first])) {
                $prefix = $replacements[$first];

                return $prefix->mergeWith($name);
            }

            return null;
        });
    }
}
