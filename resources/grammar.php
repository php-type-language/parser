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
        26 => new \Phplrt\Parser\Grammar\Concatenation([85]),
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
        50 => new \Phplrt\Parser\Grammar\Concatenation([55, 52, 56]),
        51 => new \Phplrt\Parser\Grammar\Concatenation([52, 59]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        54 => new \Phplrt\Parser\Grammar\Concatenation([53, 52]),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        56 => new \Phplrt\Parser\Grammar\Repetition(54, 0, INF),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        58 => new \Phplrt\Parser\Grammar\Concatenation([57, 52]),
        59 => new \Phplrt\Parser\Grammar\Repetition(58, 0, INF),
        60 => new \Phplrt\Parser\Grammar\Concatenation([67, 71, 72]),
        61 => new \Phplrt\Parser\Grammar\Concatenation([81, 26]),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        63 => new \Phplrt\Parser\Grammar\Optional(60),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        65 => new \Phplrt\Parser\Grammar\Optional(61),
        66 => new \Phplrt\Parser\Grammar\Concatenation([9, 62, 63, 64, 65]),
        67 => new \Phplrt\Parser\Grammar\Concatenation([73]),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        69 => new \Phplrt\Parser\Grammar\Concatenation([68, 67]),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        71 => new \Phplrt\Parser\Grammar\Repetition(69, 0, INF),
        72 => new \Phplrt\Parser\Grammar\Optional(70),
        73 => new \Phplrt\Parser\Grammar\Alternation([75, 76]),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        75 => new \Phplrt\Parser\Grammar\Concatenation([74, 26]),
        76 => new \Phplrt\Parser\Grammar\Concatenation([26, 80]),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        79 => new \Phplrt\Parser\Grammar\Alternation([77, 78]),
        80 => new \Phplrt\Parser\Grammar\Optional(79),
        81 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        82 => new \Phplrt\Parser\Grammar\Alternation([25, 39]),
        83 => new \Phplrt\Parser\Grammar\Optional(82),
        84 => new \Phplrt\Parser\Grammar\Concatenation([9, 83]),
        85 => new \Phplrt\Parser\Grammar\Concatenation([86]),
        86 => new \Phplrt\Parser\Grammar\Concatenation([87, 90]),
        87 => new \Phplrt\Parser\Grammar\Concatenation([91, 94]),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        89 => new \Phplrt\Parser\Grammar\Concatenation([88, 86]),
        90 => new \Phplrt\Parser\Grammar\Optional(89),
        91 => new \Phplrt\Parser\Grammar\Concatenation([95]),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_AND', false),
        93 => new \Phplrt\Parser\Grammar\Concatenation([92, 87]),
        94 => new \Phplrt\Parser\Grammar\Optional(93),
        95 => new \Phplrt\Parser\Grammar\Alternation([98, 101]),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        98 => new \Phplrt\Parser\Grammar\Concatenation([97, 96]),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        100 => new \Phplrt\Parser\Grammar\Optional(99),
        101 => new \Phplrt\Parser\Grammar\Concatenation([96, 100]),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        104 => new \Phplrt\Parser\Grammar\Concatenation([102, 26, 103]),
        96 => new \Phplrt\Parser\Grammar\Alternation([104, 6, 66, 84])
    ],
    'reducers' => [
        0 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::parse($token->getValue());
        },
        1 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        2 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        3 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        4 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        5 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 3) {
            return new Node\Stmt\ClassConstMaskNode(
                $children[0],
                $children[1]->getValue(),
            );
        }
    
        if ($children[1]->getName() === 'T_ASTERISK') {
            return new Node\Stmt\ClassConstMaskNode($children[0]);
        }
    
        return new Node\Stmt\ClassConstNode(
            $children[0],
            $children[1]->getValue()
        );
        },
        25 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParametersListNode($children);
        },
        17 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParameterNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        39 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $isSealed = \array_pop($children);
    
        return new Node\Stmt\Shape\ArgumentsListNode($children, $isSealed);
        },
        31 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        41 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ArgumentNode(
            $children[1],
            $children[0],
            true,
        );
        },
        42 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ArgumentNode(
            $children[1],
            $children[0],
        );
        },
        43 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ArgumentNode(
            $children[0],
        );
        },
        44 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\StringLiteralNode => $children,
            $children instanceof Node\Literal\IntLiteralNode,
            $children instanceof Node\Literal\BoolLiteralNode,
            $children instanceof Node\Literal\NullLiteralNode,
                => new Node\Literal\StringLiteralNode($children->raw),
            default => new Node\Literal\StringLiteralNode($children->getValue()),
        };
        },
        50 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\FullQualifiedName($children);
        },
        51 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Name($children);
        },
        52 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return $children->getValue();
        },
        66 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $name = \array_shift($children);
    
        $arguments = isset($children[0]) && $children[0] instanceof Node\Stmt\Callable\ArgumentsListNode
            ? \array_shift($children)
            : new Node\Stmt\Callable\ArgumentsListNode();
    
        return new Node\Stmt\CallableTypeNode(
            name: $name,
            arguments: $arguments,
            type: isset($children[0]) ? $children[0] : null,
        );
        },
        60 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode(
            list: $children,
        );
        },
        73 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\Callable\ArgumentNode(
                type: $children[1],
                modifier: Node\Stmt\Callable\Modifier::VARIADIC,
            );
        }
    
        return $children;
        },
        76 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $modifier = null;
        if (isset($children[1])) {
            $modifier = $children[1]->getName() === 'T_EQ'
                ? Node\Stmt\Callable\Modifier::OPTIONAL
                : Node\Stmt\Callable\Modifier::VARIADIC
                ;
        }
    
        return new Node\Stmt\Callable\ArgumentNode(
            type: $children[0],
            modifier: $modifier,
        );
        },
        84 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $arguments = $parameters = null;
    
        $options = \end($children);
        if ($options instanceof Node\Stmt\Shape\ArgumentsListNode) {
            $arguments = $options;
        } elseif ($options instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = $options;
        }
    
        return new Node\Stmt\NamedTypeNode(
            $children[0],
            $parameters,
            $arguments,
        );
        },
        86 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        87 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        95 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $statement = $children[0] instanceof Node\Stmt\Statement
                ? $children[0]
                : $children[1]
                ;
    
            return new Node\Stmt\NullableTypeNode($statement);
        }
    
        return $children[0];
        }
    ]
];