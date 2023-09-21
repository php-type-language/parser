<?php

use TypeLang\Parser\Node;

return [
    'initial' => 25,
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
            'T_SQUARE_BRACKET_OPEN' => '\\[',
            'T_SQUARE_BRACKET_CLOSE' => '\\]',
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
        5 => new \Phplrt\Parser\Grammar\Concatenation([9, 14, 15]),
        6 => new \Phplrt\Parser\Grammar\Alternation([0, 1, 2, 3, 4, 5]),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        9 => new \Phplrt\Parser\Grammar\Alternation([48, 49]),
        10 => new \Phplrt\Parser\Grammar\Alternation([57, 58, 59]),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        12 => new \Phplrt\Parser\Grammar\Concatenation([10, 11]),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        15 => new \Phplrt\Parser\Grammar\Alternation([12, 10, 13]),
        16 => new \Phplrt\Parser\Grammar\Concatenation([25]),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        18 => new \Phplrt\Parser\Grammar\Concatenation([17, 16]),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        21 => new \Phplrt\Parser\Grammar\Repetition(18, 0, INF),
        22 => new \Phplrt\Parser\Grammar\Optional(19),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        24 => new \Phplrt\Parser\Grammar\Concatenation([20, 16, 21, 22, 23]),
        25 => new \Phplrt\Parser\Grammar\Concatenation([85]),
        26 => new \Phplrt\Parser\Grammar\Concatenation([40]),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        28 => new \Phplrt\Parser\Grammar\Concatenation([27, 26]),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        30 => new \Phplrt\Parser\Grammar\Optional(39),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        33 => new \Phplrt\Parser\Grammar\Optional(26),
        34 => new \Phplrt\Parser\Grammar\Repetition(28, 0, INF),
        35 => new \Phplrt\Parser\Grammar\Optional(29),
        36 => new \Phplrt\Parser\Grammar\Optional(31),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        38 => new \Phplrt\Parser\Grammar\Concatenation([32, 33, 34, 35, 30, 36, 37]),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        40 => new \Phplrt\Parser\Grammar\Alternation([46, 43]),
        41 => new \Phplrt\Parser\Grammar\Alternation([10, 2, 3, 4, 0]),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        43 => new \Phplrt\Parser\Grammar\Concatenation([47]),
        44 => new \Phplrt\Parser\Grammar\Optional(42),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        46 => new \Phplrt\Parser\Grammar\Concatenation([41, 44, 45, 43]),
        47 => new \Phplrt\Parser\Grammar\Concatenation([25]),
        48 => new \Phplrt\Parser\Grammar\Concatenation([52, 10, 53]),
        49 => new \Phplrt\Parser\Grammar\Concatenation([10, 56]),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        51 => new \Phplrt\Parser\Grammar\Concatenation([50, 10]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        53 => new \Phplrt\Parser\Grammar\Repetition(51, 0, INF),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        55 => new \Phplrt\Parser\Grammar\Concatenation([54, 10]),
        56 => new \Phplrt\Parser\Grammar\Repetition(55, 0, INF),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        60 => new \Phplrt\Parser\Grammar\Concatenation([67, 71, 72]),
        61 => new \Phplrt\Parser\Grammar\Concatenation([81, 25]),
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
        75 => new \Phplrt\Parser\Grammar\Concatenation([74, 25]),
        76 => new \Phplrt\Parser\Grammar\Concatenation([25, 80]),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        79 => new \Phplrt\Parser\Grammar\Alternation([77, 78]),
        80 => new \Phplrt\Parser\Grammar\Optional(79),
        81 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        82 => new \Phplrt\Parser\Grammar\Optional(24),
        83 => new \Phplrt\Parser\Grammar\Optional(38),
        84 => new \Phplrt\Parser\Grammar\Concatenation([9, 82, 83]),
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
        95 => new \Phplrt\Parser\Grammar\Alternation([98, 99]),
        96 => new \Phplrt\Parser\Grammar\Concatenation([102, 106]),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        98 => new \Phplrt\Parser\Grammar\Concatenation([97, 96]),
        99 => new \Phplrt\Parser\Grammar\Concatenation([96, 101]),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        101 => new \Phplrt\Parser\Grammar\Optional(100),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        104 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        105 => new \Phplrt\Parser\Grammar\Concatenation([103, 104]),
        106 => new \Phplrt\Parser\Grammar\Repetition(105, 0, INF),
        107 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        109 => new \Phplrt\Parser\Grammar\Concatenation([107, 25, 108]),
        102 => new \Phplrt\Parser\Grammar\Alternation([109, 6, 66, 84])
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
        24 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParametersListNode($children);
        },
        16 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParameterNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        38 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $isSealed = \array_pop($children);
    
        return new Node\Stmt\Shape\FieldsListNode($children, $isSealed);
        },
        30 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        40 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            // In case of "nullable" suffix defined
            if (\count($children) === 3) {
                return new Node\Stmt\Shape\OptionalFieldNode(
                    new Node\Stmt\Shape\NamedFieldNode($children[0], $children[2])
                );
            }
    
            return new Node\Stmt\Shape\NamedFieldNode($children[0], $children[1]);
        }
    
        return $children;
        },
        43 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        41 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\StringLiteralNode => $children,
            $children instanceof Node\Literal\IntLiteralNode,
            $children instanceof Node\Literal\BoolLiteralNode,
            $children instanceof Node\Literal\NullLiteralNode,
                => new Node\Literal\StringLiteralNode($children->raw),
            default => new Node\Literal\StringLiteralNode($children->getValue()),
        };
        },
        48 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\FullQualifiedName($parts);
        },
        49 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\Name($parts);
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
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        73 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\Callable\VariadicArgumentNode(
                new Node\Stmt\Callable\ArgumentNode($children[1]),
            );
        }
    
        return $children;
        },
        76 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (!isset($children[1])) {
            return $argument;
        }
    
        if ($children[1]->getName() === 'T_EQ') {
            return new Node\Stmt\Callable\OptionalArgumentNode($argument);
        }
    
        return new Node\Stmt\Callable\VariadicArgumentNode($argument);
        },
        84 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $fields = $parameters = null;
    
        // Shape fields
        if (\end($children) instanceof Node\Stmt\Shape\FieldsListNode) {
            $fields = \array_pop($children);
        }
    
        // Template parameters
        if (\end($children) instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\NamedTypeNode(
            $children[0],
            $parameters,
            $fields,
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
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        99 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Stmt\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        96 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];