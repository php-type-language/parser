<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;

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
        0 => new \Phplrt\Parser\Grammar\Alternation([8, 9]),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        5 => new \Phplrt\Parser\Grammar\Concatenation([10, 15, 16]),
        6 => new \Phplrt\Parser\Grammar\Alternation([0, 1, 2, 3, 4, 5]),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        10 => new \Phplrt\Parser\Grammar\Alternation([58, 59]),
        11 => new \Phplrt\Parser\Grammar\Alternation([67, 68, 69]),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        13 => new \Phplrt\Parser\Grammar\Concatenation([11, 12]),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        16 => new \Phplrt\Parser\Grammar\Alternation([13, 11, 14]),
        17 => new \Phplrt\Parser\Grammar\Concatenation([26]),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        19 => new \Phplrt\Parser\Grammar\Concatenation([18, 17]),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        22 => new \Phplrt\Parser\Grammar\Repetition(19, 0, INF),
        23 => new \Phplrt\Parser\Grammar\Optional(20),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        25 => new \Phplrt\Parser\Grammar\Concatenation([21, 17, 22, 23, 24]),
        26 => new \Phplrt\Parser\Grammar\Concatenation([99]),
        27 => new \Phplrt\Parser\Grammar\Concatenation([46, 49]),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        30 => new \Phplrt\Parser\Grammar\Concatenation([28, 29]),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        32 => new \Phplrt\Parser\Grammar\Optional(30),
        33 => new \Phplrt\Parser\Grammar\Optional(31),
        34 => new \Phplrt\Parser\Grammar\Concatenation([27, 32, 33]),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        37 => new \Phplrt\Parser\Grammar\Optional(35),
        38 => new \Phplrt\Parser\Grammar\Concatenation([36, 37]),
        39 => new \Phplrt\Parser\Grammar\Alternation([34, 38]),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        41 => new \Phplrt\Parser\Grammar\Optional(39),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        43 => new \Phplrt\Parser\Grammar\Concatenation([40, 41, 42]),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        45 => new \Phplrt\Parser\Grammar\Optional(44),
        46 => new \Phplrt\Parser\Grammar\Concatenation([50]),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        48 => new \Phplrt\Parser\Grammar\Concatenation([47, 46]),
        49 => new \Phplrt\Parser\Grammar\Repetition(48, 0, INF),
        50 => new \Phplrt\Parser\Grammar\Alternation([56, 53]),
        51 => new \Phplrt\Parser\Grammar\Alternation([11, 2, 3, 4, 0]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        53 => new \Phplrt\Parser\Grammar\Concatenation([57]),
        54 => new \Phplrt\Parser\Grammar\Optional(52),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        56 => new \Phplrt\Parser\Grammar\Concatenation([51, 54, 55, 53]),
        57 => new \Phplrt\Parser\Grammar\Concatenation([26]),
        58 => new \Phplrt\Parser\Grammar\Concatenation([62, 11, 63]),
        59 => new \Phplrt\Parser\Grammar\Concatenation([11, 66]),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        61 => new \Phplrt\Parser\Grammar\Concatenation([60, 11]),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        63 => new \Phplrt\Parser\Grammar\Repetition(61, 0, INF),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        65 => new \Phplrt\Parser\Grammar\Concatenation([64, 11]),
        66 => new \Phplrt\Parser\Grammar\Repetition(65, 0, INF),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        70 => new \Phplrt\Parser\Grammar\Concatenation([77, 81, 82]),
        71 => new \Phplrt\Parser\Grammar\Concatenation([95, 26]),
        72 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        73 => new \Phplrt\Parser\Grammar\Optional(70),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        75 => new \Phplrt\Parser\Grammar\Optional(71),
        76 => new \Phplrt\Parser\Grammar\Concatenation([10, 72, 73, 74, 75]),
        77 => new \Phplrt\Parser\Grammar\Concatenation([83, 84]),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        79 => new \Phplrt\Parser\Grammar\Concatenation([78, 77]),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        81 => new \Phplrt\Parser\Grammar\Repetition(79, 0, INF),
        82 => new \Phplrt\Parser\Grammar\Optional(80),
        83 => new \Phplrt\Parser\Grammar\Concatenation([85, 87]),
        84 => new \Phplrt\Parser\Grammar\Optional(7),
        85 => new \Phplrt\Parser\Grammar\Alternation([89, 90]),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        87 => new \Phplrt\Parser\Grammar\Optional(86),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        89 => new \Phplrt\Parser\Grammar\Concatenation([88, 26]),
        90 => new \Phplrt\Parser\Grammar\Concatenation([26, 94]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        93 => new \Phplrt\Parser\Grammar\Alternation([91, 92]),
        94 => new \Phplrt\Parser\Grammar\Optional(93),
        95 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        96 => new \Phplrt\Parser\Grammar\Optional(25),
        97 => new \Phplrt\Parser\Grammar\Optional(43),
        98 => new \Phplrt\Parser\Grammar\Concatenation([10, 96, 97]),
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
        123 => new \Phplrt\Parser\Grammar\Concatenation([121, 26, 122]),
        116 => new \Phplrt\Parser\Grammar\Alternation([123, 6, 76, 98])
    ],
    'reducers' => [
        7 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        8 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        9 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        1 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        2 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        3 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        4 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        5 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        25 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParametersListNode($children);
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParameterNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        43 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        45 => function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        27 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \ArrayObject($children);
        },
        50 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        53 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        51 => function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\IntLiteralNode,
            $children instanceof Node\Literal\StringLiteralNode => $children,
            $children instanceof Node\Literal\BoolLiteralNode,
            $children instanceof Node\Literal\NullLiteralNode,
                => new Node\Literal\StringLiteralNode($children->raw),
            default => new Node\Literal\StringLiteralNode($children->getValue()),
        };
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\FullQualifiedName($parts);
        },
        59 => function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\Name($parts);
        },
        76 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        70 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        77 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\NamedArgumentNode($children[1], $children[0]);
        },
        83 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\OutArgumentNode($children[0]);
        },
        85 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\Callable\VariadicArgumentNode(
                new Node\Stmt\Callable\ArgumentNode($children[1]),
            );
        }
    
        return $children;
        },
        90 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (!isset($children[1])) {
            return $argument;
        }
    
        if ($children[1]->getName() === 'T_EQ') {
            return new Node\Stmt\Callable\OptionalArgumentNode($argument);
        }
    
        return new Node\Stmt\Callable\VariadicArgumentNode($argument);
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