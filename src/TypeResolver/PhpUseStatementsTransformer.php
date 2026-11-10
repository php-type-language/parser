<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

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
 * Then, if you apply it to a name, you will get normalized substitutions:
 * ```
 * // Error\SemanticException => TypeLang\Parser\Exception\SemanticException
 * // Node                    => TypeLang\Parser\Node
 * ```
 */
final class PhpUseStatementsTransformer
{
    /**
     * The words of the imported names by the lowercase alias.
     *
     * @var array<non-empty-lowercase-string, non-empty-list<non-empty-string>>
     */
    private readonly array $replacements;

    /**
     * @param iterable<non-empty-string|array-key, non-empty-string> $replacements
     */
    public function __construct(
        iterable $replacements,
    ) {
        $this->replacements = $this->format($replacements);
    }

    /**
     * @param iterable<array-key, non-empty-string> $replacements
     * @return array<non-empty-lowercase-string, non-empty-list<non-empty-string>>
     */
    private function format(iterable $replacements): array
    {
        $result = [];

        foreach ($replacements as $alias => $import) {
            $import = \ltrim($import, NamespaceName::DELIMITER);

            if ($import === '') {
                throw new \InvalidArgumentException('Import must contain at least one name segment');
            }

            /** @var non-empty-list<non-empty-string> $words */
            $words = \explode(NamespaceName::DELIMITER, $import);

            // An import without an alias is known by its last word
            if (\is_int($alias) || $alias === '') {
                $alias = \end($words);
            }

            $result[\strtolower($alias)] = $words;
        }

        return $result;
    }

    private static function isNonSubstituted(Name $name): bool
    {
        // Names written from the root, or with the "namespace" keyword, and
        // the special ones (self, parent, static) must not be replaced
        return $name->isFullyQualified()
            || $name->namespace?->kind === NamespaceKind::NamespaceRelative
            || $name->isSpecial()
            || $name->isBuiltin();
    }

    public function __invoke(Name $name): ?Name
    {
        if (self::isNonSubstituted($name)) {
            return null;
        }

        $import = $this->replacements[\strtolower($name->first())] ?? null;

        if ($import === null) {
            return null;
        }

        // The first word is the alias, it is replaced with the imported name
        $words = [...$import, ...\array_slice($name->toArray(), 1)];
        $last = \array_pop($words);

        if ($words === []) {
            return new Name($last, null, $name->offset);
        }

        return new Name($last, new NamespaceName(\implode(NamespaceName::DELIMITER, $words)), $name->offset);
    }
}
