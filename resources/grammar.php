<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;

return [
    'initial' => 28,
    'tokens' => [
        'default' => [
            'T_DQ_STRING_LITERAL' => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL' => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_FLOAT_LITERAL' => '(?i)(?:-?[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?)|(?:-?[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?)|(?:-?[0-9]++e-?[0-9]++)',
            'T_INT_LITERAL' => '\\-?(?i)(?:(?:0b[0-1_]++)|(?:0o[0-7_]++)|(?:0x[0-9a-f_]++)|(?:[0-9][0-9_]*+))',
            'T_BOOL_LITERAL' => '\\b(?i)(?:true|false)\\b',
            'T_NULL_LITERAL' => '\\b(?i)(?:null)\\b',
            'T_VARIABLE' => '\\$[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
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
            'T_AMP' => '&',
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
        0 => new \Phplrt\Parser\Grammar\Alternation([9, 10]),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        5 => new \Phplrt\Parser\Grammar\Concatenation([11, 12]),
        6 => new \Phplrt\Parser\Grammar\Concatenation([11, 17, 18]),
        7 => new \Phplrt\Parser\Grammar\Alternation([0, 1, 2, 3, 4, 5, 6]),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        11 => new \Phplrt\Parser\Grammar\Alternation([60, 61]),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        13 => new \Phplrt\Parser\Grammar\Alternation([69, 70, 71]),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        15 => new \Phplrt\Parser\Grammar\Concatenation([13, 14]),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        18 => new \Phplrt\Parser\Grammar\Alternation([15, 13, 16]),
        19 => new \Phplrt\Parser\Grammar\Concatenation([28]),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        21 => new \Phplrt\Parser\Grammar\Concatenation([20, 19]),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        24 => new \Phplrt\Parser\Grammar\Repetition(21, 0, INF),
        25 => new \Phplrt\Parser\Grammar\Optional(22),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        27 => new \Phplrt\Parser\Grammar\Concatenation([23, 19, 24, 25, 26]),
        28 => new \Phplrt\Parser\Grammar\Concatenation([99]),
        29 => new \Phplrt\Parser\Grammar\Concatenation([48, 51]),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        32 => new \Phplrt\Parser\Grammar\Concatenation([30, 31]),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        34 => new \Phplrt\Parser\Grammar\Optional(32),
        35 => new \Phplrt\Parser\Grammar\Optional(33),
        36 => new \Phplrt\Parser\Grammar\Concatenation([29, 34, 35]),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        39 => new \Phplrt\Parser\Grammar\Optional(37),
        40 => new \Phplrt\Parser\Grammar\Concatenation([38, 39]),
        41 => new \Phplrt\Parser\Grammar\Alternation([36, 40]),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        43 => new \Phplrt\Parser\Grammar\Optional(41),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        45 => new \Phplrt\Parser\Grammar\Concatenation([42, 43, 44]),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        47 => new \Phplrt\Parser\Grammar\Optional(46),
        48 => new \Phplrt\Parser\Grammar\Concatenation([52]),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        50 => new \Phplrt\Parser\Grammar\Concatenation([49, 48]),
        51 => new \Phplrt\Parser\Grammar\Repetition(50, 0, INF),
        52 => new \Phplrt\Parser\Grammar\Alternation([58, 55]),
        53 => new \Phplrt\Parser\Grammar\Alternation([13, 2, 3, 4, 0]),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        55 => new \Phplrt\Parser\Grammar\Concatenation([59]),
        56 => new \Phplrt\Parser\Grammar\Optional(54),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        58 => new \Phplrt\Parser\Grammar\Concatenation([53, 56, 57, 55]),
        59 => new \Phplrt\Parser\Grammar\Concatenation([28]),
        60 => new \Phplrt\Parser\Grammar\Concatenation([64, 13, 65]),
        61 => new \Phplrt\Parser\Grammar\Concatenation([13, 68]),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        63 => new \Phplrt\Parser\Grammar\Concatenation([62, 13]),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        65 => new \Phplrt\Parser\Grammar\Repetition(63, 0, INF),
        66 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        67 => new \Phplrt\Parser\Grammar\Concatenation([66, 13]),
        68 => new \Phplrt\Parser\Grammar\Repetition(67, 0, INF),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        72 => new \Phplrt\Parser\Grammar\Concatenation([79, 83, 84]),
        73 => new \Phplrt\Parser\Grammar\Concatenation([95, 28]),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        75 => new \Phplrt\Parser\Grammar\Optional(72),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        77 => new \Phplrt\Parser\Grammar\Optional(73),
        78 => new \Phplrt\Parser\Grammar\Concatenation([11, 74, 75, 76, 77]),
        79 => new \Phplrt\Parser\Grammar\Concatenation([85, 87]),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        81 => new \Phplrt\Parser\Grammar\Concatenation([80, 79]),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        83 => new \Phplrt\Parser\Grammar\Repetition(81, 0, INF),
        84 => new \Phplrt\Parser\Grammar\Optional(82),
        85 => new \Phplrt\Parser\Grammar\Concatenation([88, 89]),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        87 => new \Phplrt\Parser\Grammar\Optional(86),
        88 => new \Phplrt\Parser\Grammar\Concatenation([90, 92]),
        89 => new \Phplrt\Parser\Grammar\Optional(8),
        90 => new \Phplrt\Parser\Grammar\Concatenation([28, 94]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        92 => new \Phplrt\Parser\Grammar\Optional(91),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        94 => new \Phplrt\Parser\Grammar\Optional(93),
        95 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        96 => new \Phplrt\Parser\Grammar\Optional(27),
        97 => new \Phplrt\Parser\Grammar\Optional(45),
        98 => new \Phplrt\Parser\Grammar\Concatenation([11, 96, 97]),
        99 => new \Phplrt\Parser\Grammar\Concatenation([100]),
        100 => new \Phplrt\Parser\Grammar\Concatenation([101, 104]),
        101 => new \Phplrt\Parser\Grammar\Concatenation([105, 108]),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        103 => new \Phplrt\Parser\Grammar\Concatenation([102, 100]),
        104 => new \Phplrt\Parser\Grammar\Optional(103),
        105 => new \Phplrt\Parser\Grammar\Concatenation([109]),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        107 => new \Phplrt\Parser\Grammar\Concatenation([106, 101]),
        108 => new \Phplrt\Parser\Grammar\Optional(107),
        109 => new \Phplrt\Parser\Grammar\Alternation([112, 113]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([116, 120]),
        111 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        112 => new \Phplrt\Parser\Grammar\Concatenation([111, 110]),
        113 => new \Phplrt\Parser\Grammar\Concatenation([110, 115]),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        115 => new \Phplrt\Parser\Grammar\Optional(114),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        119 => new \Phplrt\Parser\Grammar\Concatenation([117, 118]),
        120 => new \Phplrt\Parser\Grammar\Repetition(119, 0, INF),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        122 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        123 => new \Phplrt\Parser\Grammar\Concatenation([121, 28, 122]),
        116 => new \Phplrt\Parser\Grammar\Alternation([123, 7, 78, 98])
    ],
    'reducers' => [
        8 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        0 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->stringPool[$token] ??= $children;
        },
        9 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        10 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        1 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        2 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        3 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        4 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        5 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        6 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        27 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParametersListNode($children);
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParameterNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        45 => function (\Phplrt\Parser\Context $ctx, $children) {
            if ($children === []) {
            return new Node\Stmt\Shape\FieldsListNode();
        }
    
        if (!$children[0] instanceof \ArrayObject) {
            return new Node\Stmt\Shape\FieldsListNode([], false);
        }
    
        return new Node\Stmt\Shape\FieldsListNode(
            $children[0]->getArrayCopy(),
            \count($children) !== 2,
        );
        },
        47 => function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        29 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \ArrayObject($children);
        },
        52 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            $value = \array_pop($children);
            $field = $children[0] instanceof Node\Literal\IntLiteralNode
                ? new Node\Stmt\Shape\NumericFieldNode($children[0], $value)
                : new Node\Stmt\Shape\NamedFieldNode($children[0], $value)
            ;
    
            // In case of "nullable" suffix defined
            if (\count($children) === 2) {
                return new Node\Stmt\Shape\OptionalFieldNode($field);
            }
    
            return $field;
        }
    
        return $children;
        },
        55 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        53 => function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\IntLiteralNode,
            $children instanceof Node\Literal\StringLiteralNode => $children,
            $children instanceof Node\Literal\BoolLiteralNode,
            $children instanceof Node\Literal\NullLiteralNode,
                => new Node\Literal\StringLiteralNode($children->raw),
            default => new Node\Literal\StringLiteralNode($children->getValue()),
        };
        },
        60 => function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\FullQualifiedName($parts);
        },
        61 => function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\Name($parts);
        },
        78 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        72 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        79 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (!isset($children[1])) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\OptionalArgumentNode($children[0]);
        },
        85 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\NamedArgumentNode($children[1], $children[0]);
        },
        88 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (!isset($children[1])) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\VariadicArgumentNode($children[0]);
        },
        90 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (\count($children) === 1) {
            return $argument;
        }
    
        return new Node\Stmt\Callable\OutArgumentNode($argument);
        },
        98 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        100 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        101 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        109 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        113 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Stmt\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        110 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];