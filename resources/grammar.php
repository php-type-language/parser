<?php

use TypeLang\Parser\Node;

return [
    'initial' => 26,
    'tokens' => [
        'default' => [
            'T_DQ_STRING_LITERAL' => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL' => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_FLOAT_LITERAL' => '(?i)(?:-?[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?)|(?:-?[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?)|(?:-?[0-9]++e-?[0-9]++)',
            'T_INT_LITERAL' => '\\-?(?i)(?:(?:0b[0-1_]++)|(?:0o[0-7_]++)|(?:0x[0-9a-f_]++)|(?:[0-9][0-9_]*+))',
            'T_BOOL_LITERAL' => '\\b(?i)(?:true|false)\\b',
            'T_NULL_LITERAL' => '\\b(?i)(?:null)\\b',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_ANGLE_BRACKET_OPEN' => '<',
            'T_ANGLE_BRACKET_CLOSE' => '>',
            'T_PARENTHESIS_OPEN' => '\\(',
            'T_PARENTHESIS_CLOSE' => '\\)',
            'T_BRACE_OPEN' => '\\{',
            'T_BRACE_CLOSE' => '\\}',
            'T_COMMA' => ',',
            'T_ELLIPSIS' => '\\.\\.\\.',
            'T_DOUBLE_COLON' => '::',
            'T_COLON' => ':',
            'T_EQ' => '=',
            'T_NS_DELIMITER' => '\\\\',
            'T_NULLABLE' => '\\?',
            'T_NOT' => '\\!',
            'T_OR' => '\\|',
            'T_AND' => '&',
            'T_ASTERISK' => '\\*',
            'T_WHITESPACE' => '\\s+',
            'T_BLOCK_COMMENT' => '\\h*/\\*.*?\\*/\\h*',
        ],
    ],
    'skip' => [
        'T_WHITESPACE',
        'T_BLOCK_COMMENT',
    ],
    'transitions' => [
        
    ],
    'grammar' => [
        0 => new \Phplrt\Parser\Grammar\Alternation([7, 8]),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        5 => new \Phplrt\Parser\Grammar\Concatenation([9, 15, 16]),
        6 => new \Phplrt\Parser\Grammar\Alternation([0, 1, 2, 3, 4, 5]),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        9 => new \Phplrt\Parser\Grammar\Alternation([50, 51]),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        12 => new \Phplrt\Parser\Grammar\Concatenation([10, 11]),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        16 => new \Phplrt\Parser\Grammar\Alternation([12, 13, 14]),
        17 => new \Phplrt\Parser\Grammar\Concatenation([26]),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        19 => new \Phplrt\Parser\Grammar\Concatenation([18, 17]),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        22 => new \Phplrt\Parser\Grammar\Repetition(19, 0, INF),
        23 => new \Phplrt\Parser\Grammar\Optional(20),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        25 => new \Phplrt\Parser\Grammar\Concatenation([21, 17, 22, 23, 24]),
        26 => new \Phplrt\Parser\Grammar\Concatenation([88]),
        27 => new \Phplrt\Parser\Grammar\Alternation([41, 42, 43]),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        29 => new \Phplrt\Parser\Grammar\Concatenation([28, 27]),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        31 => new \Phplrt\Parser\Grammar\Optional(40),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        34 => new \Phplrt\Parser\Grammar\Optional(27),
        35 => new \Phplrt\Parser\Grammar\Repetition(29, 0, INF),
        36 => new \Phplrt\Parser\Grammar\Optional(30),
        37 => new \Phplrt\Parser\Grammar\Optional(32),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        39 => new \Phplrt\Parser\Grammar\Concatenation([33, 34, 35, 36, 31, 37, 38]),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        41 => new \Phplrt\Parser\Grammar\Concatenation([44, 46, 47, 45]),
        42 => new \Phplrt\Parser\Grammar\Concatenation([44, 48, 45]),
        43 => new \Phplrt\Parser\Grammar\Concatenation([45]),
        44 => new \Phplrt\Parser\Grammar\Alternation([49, 2, 3, 4, 0]),
        45 => new \Phplrt\Parser\Grammar\Concatenation([26]),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', false),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        50 => new \Phplrt\Parser\Grammar\Concatenation([55, 56, 57]),
        51 => new \Phplrt\Parser\Grammar\Concatenation([61, 62]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        54 => new \Phplrt\Parser\Grammar\Concatenation([52, 53]),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        57 => new \Phplrt\Parser\Grammar\Repetition(54, 0, INF),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        60 => new \Phplrt\Parser\Grammar\Concatenation([58, 59]),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        62 => new \Phplrt\Parser\Grammar\Repetition(60, 0, INF),
        63 => new \Phplrt\Parser\Grammar\Concatenation([70, 74, 75]),
        64 => new \Phplrt\Parser\Grammar\Concatenation([84, 26]),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        66 => new \Phplrt\Parser\Grammar\Optional(63),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        68 => new \Phplrt\Parser\Grammar\Optional(64),
        69 => new \Phplrt\Parser\Grammar\Concatenation([9, 65, 66, 67, 68]),
        70 => new \Phplrt\Parser\Grammar\Concatenation([76]),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        72 => new \Phplrt\Parser\Grammar\Concatenation([71, 70]),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        74 => new \Phplrt\Parser\Grammar\Repetition(72, 0, INF),
        75 => new \Phplrt\Parser\Grammar\Optional(73),
        76 => new \Phplrt\Parser\Grammar\Alternation([78, 79]),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        78 => new \Phplrt\Parser\Grammar\Concatenation([77, 26]),
        79 => new \Phplrt\Parser\Grammar\Concatenation([26, 83]),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        81 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        82 => new \Phplrt\Parser\Grammar\Alternation([80, 81]),
        83 => new \Phplrt\Parser\Grammar\Optional(82),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        85 => new \Phplrt\Parser\Grammar\Alternation([25, 39]),
        86 => new \Phplrt\Parser\Grammar\Optional(85),
        87 => new \Phplrt\Parser\Grammar\Concatenation([9, 86]),
        88 => new \Phplrt\Parser\Grammar\Concatenation([89]),
        89 => new \Phplrt\Parser\Grammar\Concatenation([90, 93]),
        90 => new \Phplrt\Parser\Grammar\Concatenation([94, 97]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        92 => new \Phplrt\Parser\Grammar\Concatenation([91, 89]),
        93 => new \Phplrt\Parser\Grammar\Optional(92),
        94 => new \Phplrt\Parser\Grammar\Concatenation([98]),
        95 => new \Phplrt\Parser\Grammar\Lexeme('T_AND', false),
        96 => new \Phplrt\Parser\Grammar\Concatenation([95, 90]),
        97 => new \Phplrt\Parser\Grammar\Optional(96),
        98 => new \Phplrt\Parser\Grammar\Alternation([101, 104]),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        101 => new \Phplrt\Parser\Grammar\Concatenation([100, 99]),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        103 => new \Phplrt\Parser\Grammar\Optional(102),
        104 => new \Phplrt\Parser\Grammar\Concatenation([99, 103]),
        105 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        107 => new \Phplrt\Parser\Grammar\Concatenation([105, 26, 106]),
        99 => new \Phplrt\Parser\Grammar\Alternation([107, 6, 69, 87])
    ],
    'reducers' => [
        0 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralStmt::parse($token, $token->getName() === 'T_DQ_STRING_LITERAL');
        },
        1 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralStmt::parse($token);
        },
        2 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteralStmt::parse($token);
        },
        3 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralStmt::parse($token);
        },
        4 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralStmt($children->getValue());
        },
        5 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 3) {
            return new Node\Stmt\ClassConstMaskStmt(
                $children[0],
                $children[1]->getValue(),
            );
        }
    
        if ($children[1]->getName() === 'T_ASTERISK') {
            return new Node\Stmt\ClassConstMaskStmt($children[0]);
        }
    
        return new Node\Stmt\ClassConstStmt(
            $children[0],
            $children[1]->getValue()
        );
        },
        25 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\Parameters($children);
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\Parameter(
            \is_array($children) ? $children[0] : $children,
        );
        },
        39 => function (\Phplrt\Parser\Context $ctx, $children) {
            $isSealed = \array_pop($children);
    
        return new Node\Stmt\Shape\Arguments($children, $isSealed);
        },
        31 => function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        41 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\Argument(
            $children[1],
            $children[0],
            true,
        );
        },
        42 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\Argument(
            $children[1],
            $children[0],
        );
        },
        43 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\Argument(
            $children[0],
        );
        },
        44 => function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\StringLiteralStmt => $children,
            $children instanceof Node\Literal\IntLiteralStmt,
            $children instanceof Node\Literal\BoolLiteral,
            $children instanceof Node\Literal\NullLiteralStmt,
                => new Node\Literal\StringLiteralStmt($children->raw),
            default => new Node\Literal\StringLiteralStmt($children->getValue()),
        };
        },
        50 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\FullQualifiedName::parse($children);
        },
        51 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\Name::parse($children);
        },
        69 => function (\Phplrt\Parser\Context $ctx, $children) {
            $name = \array_shift($children);
    
        $arguments = isset($children[0]) && $children[0] instanceof Node\Stmt\Callable\Arguments
            ? \array_shift($children)
            : new Node\Stmt\Callable\Arguments();
    
        return new Node\Stmt\CallableTypeStmt(
            name: $name,
            arguments: $arguments,
            type: isset($children[0]) ? $children[0] : null,
        );
        },
        63 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\Arguments(
            list: $children,
        );
        },
        76 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\Callable\Argument(
                type: $children[1],
                modifier: Node\Stmt\Callable\Modifier::VARIADIC,
            );
        }
    
        return $children;
        },
        79 => function (\Phplrt\Parser\Context $ctx, $children) {
            $modifier = null;
        if (isset($children[1])) {
            $modifier = $children[1]->getName() === 'T_EQ'
                ? Node\Stmt\Callable\Modifier::OPTIONAL
                : Node\Stmt\Callable\Modifier::VARIADIC
                ;
        }
    
        return new Node\Stmt\Callable\Argument(
            type: $children[0],
            modifier: $modifier,
        );
        },
        87 => function (\Phplrt\Parser\Context $ctx, $children) {
            $arguments = $parameters = null;
    
        $options = \end($children);
        if ($options instanceof Node\Stmt\Shape\Arguments) {
            $arguments = $options;
        } elseif ($options instanceof Node\Stmt\Template\Parameters) {
            $parameters = $options;
        }
    
        return new Node\Stmt\NamedTypeStmt(
            name: $children[0],
            parameters: $parameters,
            arguments: $arguments,
        );
        },
        89 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeStmt($children[0], $children[1]);
        }
    
        return $children;
        },
        90 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeStmt($children[0], $children[1]);
        }
    
        return $children;
        },
        98 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $statement = $children[0] instanceof Node\Stmt\Statement
                ? $children[0]
                : $children[1]
                ;
    
            return new Node\Stmt\NullableTypeStmt($statement);
        }
    
        return $children[0];
        }
    ]
];