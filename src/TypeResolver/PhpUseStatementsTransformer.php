<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Type\Name;

/**
 * A set of rules for modifying names defined in the `use` statements of PHP code.
 *
 * For example, for such code:
 * ```
 * use TypeLang\Parser\Node;
 * use TypeLang\Parser\Exception as Error;
 * ```
 *
 * You need to create a rule like this:
 * ```
 * $transformer = new PhpUseStatementsTransformer([
 *     'TypeLang\Parser\Node',                     // for `use TypeLang\Parser\Node;`
 *     'Error' => 'TypeLang\Parser\Exception',     // for `use TypeLang\Parser\Exception as Error;`
 * ]);
 * ```
 *
 * Then, if you apply it to the finished result, you will get
 * normalized substitutions:
 * ```
 * $sourceAst = new TypeLang\Parser\Parser()
 *     ->parse(<<<'PHP'
 *         array { Node, Error\SemanticException }
 *         PHP);
 *
 * $transformedAst = new \TypeLang\Parser\TypeResolver()
 *     ->resolve($sourceAst, $transformer);
 *
 * // Expected Output:
 * // > array{
 * // >     TypeLang\Parser\Node,
 * // >     TypeLang\Parser\Exception\SemanticException
 * // > }
 * ```
 */
final readonly class PhpUseStatementsTransformer implements TransformerInterface
{
    /**
     * @var array<non-empty-lowercase-string, Name>
     */
    private array $replacements;

    /**
     * @param iterable<non-empty-string|array-key, non-empty-string|Name> $replacements
     */
    public function __construct(iterable $replacements)
    {
        $this->replacements = $this->format($replacements);
    }

    /**
     * @param iterable<array-key, non-empty-string|Name> $replacements
     * @return array<non-empty-lowercase-string, Name>
     */
    private function format(iterable $replacements): array
    {
        $result = [];

        foreach ($replacements as $key => $replacement) {
            // normalize value
            if (\is_string($replacement)) {
                $replacement = Name::createFromString($replacement);
            }

            // normalize key
            if (\is_int($key) || $key === '') {
                $key = $replacement->last->toString();
            }

            $result[\strtolower($key)] = $replacement;
        }

        return $result;
    }

    public function __invoke(Name $name): ?Name
    {
        $first = \strtolower($name->first->toString());
        $prefix = $this->replacements[$first] ?? null;

        return $prefix?->mergeWith($name);
    }
}
