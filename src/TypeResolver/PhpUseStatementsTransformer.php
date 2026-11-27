<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

/**
 * A set of rules for modifying names defined in the `use` statements of PHP
 * code.
 *
 * For example, for such code:
 * ```
 * use TypeLang\Type\Name;
 * use TypeLang\Parser\Exception as Error;
 * ```
 *
 * You need to create a rule like this:
 * ```
 * $transformer = new PhpUseStatementsTransformer([
 *     // use TypeLang\Type\Name;
 *     'TypeLang\Type\Name',
 *     // use TypeLang\Parser\Exception as Error;
 *     'Error' => 'TypeLang\Parser\Exception',
 * ]);
 * ```
 *
 * Then, if you apply it to a name, you will get normalized substitutions:
 * ```
 * // Error\ParsingException => \TypeLang\Parser\Exception\ParsingException
 * // Name                   => \TypeLang\Type\Name
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
     * @param iterable<array-key, non-empty-string> $replacements
     * @throws \InvalidArgumentException in case of an import has no name
     *         segment
     */
    public function __construct(
        iterable $replacements,
    ) {
        $this->replacements = $this->format($replacements);
    }

    /**
     * @param iterable<array-key, non-empty-string> $replacements
     * @return array<
     *     non-empty-lowercase-string,
     *     non-empty-list<non-empty-string>
     * >
     * @throws \InvalidArgumentException in case of an import has no name
     *         segment
     */
    private function format(iterable $replacements): array
    {
        $result = [];

        foreach ($replacements as $alias => $import) {
            $import = \ltrim($import, NamespaceName::DELIMITER);

            if ($import === '') {
                throw new \InvalidArgumentException('Import must contain at least one name segment');
            }

            /** @var non-empty-list<non-empty-string> */
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
        // Names written from the root, or with the `namespace` keyword, and
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

        $namespace = new NamespaceName(
            value: \implode(NamespaceName::DELIMITER, $words),
            kind: NamespaceKind::FullyQualified,
        );

        return new Name($last, $namespace, $name->offset);
    }
}
