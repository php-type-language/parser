<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'binary_operator_spaces' => [
            'default' => null,
            'operators' => [
                '|' => 'single_space',
                '!==' => 'single_space',
                '!=' => 'single_space',
                '==' => 'single_space',
                '===' => 'single_space',
            ],
        ],
        'modernize_strpos' => true,
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
            ],
        ],
        'declare_strict_types' => true,
        'linebreak_after_opening_tag' => true,
        'blank_line_after_opening_tag' => true,
        'single_quote' => true,
        'lowercase_cast' => true,
        'short_scalar_cast' => true,
        'no_leading_import_slash' => true,
        'method_argument_space' => [
            'on_multiline' => 'ignore',
        ],
        'declare_equal_normalize' => [
            'space' => 'none',
        ],
        'new_with_braces' => true,
        'no_blank_lines_after_phpdoc' => true,
        'single_blank_line_before_namespace' => true,
        'ternary_operator_spaces' => true,
        'unary_operator_spaces' => true,
        'return_type_declaration' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_separation' => false,
        'yoda_style' => false,
        'void_return' => true,
    ])
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in([__DIR__ . '/libs', __DIR__ . '/tests'])
            ->name('*.php')
    )
;
