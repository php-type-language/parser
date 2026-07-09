<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver\PhpUseStatementsReader;

final readonly class NamespaceFinder
{
    /**
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return \Iterator<array-key, \PhpToken>
     */
    public function rewind(string $namespace, \Iterator $tokens): \Iterator
    {
        $atLeastOneNamespace = false;

        while ($tokens->valid()) {
            $current = $tokens->current();

            switch ($current->id) {
                case \T_NAMESPACE:
                    $atLeastOneNamespace = true;
                    if ($this->readNamespace($tokens) === $namespace) {
                        return $tokens;
                    }
                    break;

                case \T_USE:
                    if ($atLeastOneNamespace === false) {
                        return $tokens;
                    }
                    break;
            }

            $tokens->next();
        }

        return $tokens;
    }

    /**
     * @param \Iterator<array-key, \PhpToken> $tokens
     */
    private function readNamespace(\Iterator $tokens): string
    {
        // Skip "namespace" token.
        $tokens->next();

        $result = null;

        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_NAME_QUALIFIED || $current->id === \T_STRING) {
                $result = $current->text;
            } elseif ($current->text === ';' || $current->text === '{') {
                // A namespace name is terminated either by ";" or "{"
                $tokens->next();

                return $result ?? '';
            }

            $tokens->next();
        }

        return $result ?? '';
    }
}
