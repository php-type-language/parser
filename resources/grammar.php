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
        10 => new \Phplrt\Parser\Grammar\Alternation([49, 50]),
        11 => new \Phplrt\Parser\Grammar\Alternation([58, 59, 60]),
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
        26 => new \Phplrt\Parser\Grammar\Concatenation([90]),
        27 => new \Phplrt\Parser\Grammar\Concatenation([41]),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        29 => new \Phplrt\Parser\Grammar\Concatenation([28, 27]),
        30 => new \Phplrt\Parser\Grammar\Repetition(29, 0, INF),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        32 => new \Phplrt\Parser\Grammar\Concatenation([27, 30, 31]),
        33 => new \Phplrt\Parser\Grammar\Optional(40),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        36 => new \Phplrt\Parser\Grammar\Optional(32),
        37 => new \Phplrt\Parser\Grammar\Optional(34),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        39 => new \Phplrt\Parser\Grammar\Concatenation([35, 36, 33, 37, 38]),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        41 => new \Phplrt\Parser\Grammar\Alternation([47, 44]),
        42 => new \Phplrt\Parser\Grammar\Alternation([11, 2, 3, 4, 0]),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        44 => new \Phplrt\Parser\Grammar\Concatenation([48]),
        45 => new \Phplrt\Parser\Grammar\Optional(43),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        47 => new \Phplrt\Parser\Grammar\Concatenation([42, 45, 46, 44]),
        48 => new \Phplrt\Parser\Grammar\Concatenation([26]),
        49 => new \Phplrt\Parser\Grammar\Concatenation([53, 11, 54]),
        50 => new \Phplrt\Parser\Grammar\Concatenation([11, 57]),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        52 => new \Phplrt\Parser\Grammar\Concatenation([51, 11]),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        54 => new \Phplrt\Parser\Grammar\Repetition(52, 0, INF),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        56 => new \Phplrt\Parser\Grammar\Concatenation([55, 11]),
        57 => new \Phplrt\Parser\Grammar\Repetition(56, 0, INF),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        61 => new \Phplrt\Parser\Grammar\Concatenation([68, 72, 73]),
        62 => new \Phplrt\Parser\Grammar\Concatenation([86, 26]),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        64 => new \Phplrt\Parser\Grammar\Optional(61),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        66 => new \Phplrt\Parser\Grammar\Optional(62),
        67 => new \Phplrt\Parser\Grammar\Concatenation([10, 63, 64, 65, 66]),
        68 => new \Phplrt\Parser\Grammar\Concatenation([74, 75]),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        70 => new \Phplrt\Parser\Grammar\Concatenation([69, 68]),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        72 => new \Phplrt\Parser\Grammar\Repetition(70, 0, INF),
        73 => new \Phplrt\Parser\Grammar\Optional(71),
        74 => new \Phplrt\Parser\Grammar\Concatenation([76, 78]),
        75 => new \Phplrt\Parser\Grammar\Optional(7),
        76 => new \Phplrt\Parser\Grammar\Alternation([80, 81]),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        78 => new \Phplrt\Parser\Grammar\Optional(77),
        79 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        80 => new \Phplrt\Parser\Grammar\Concatenation([79, 26]),
        81 => new \Phplrt\Parser\Grammar\Concatenation([26, 85]),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        84 => new \Phplrt\Parser\Grammar\Alternation([82, 83]),
        85 => new \Phplrt\Parser\Grammar\Optional(84),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        87 => new \Phplrt\Parser\Grammar\Optional(25),
        88 => new \Phplrt\Parser\Grammar\Optional(39),
        89 => new \Phplrt\Parser\Grammar\Concatenation([10, 87, 88]),
        90 => new \Phplrt\Parser\Grammar\Concatenation([91]),
        91 => new \Phplrt\Parser\Grammar\Concatenation([92, 95]),
        92 => new \Phplrt\Parser\Grammar\Concatenation([96, 99]),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        94 => new \Phplrt\Parser\Grammar\Concatenation([93, 91]),
        95 => new \Phplrt\Parser\Grammar\Optional(94),
        96 => new \Phplrt\Parser\Grammar\Concatenation([100]),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        98 => new \Phplrt\Parser\Grammar\Concatenation([97, 92]),
        99 => new \Phplrt\Parser\Grammar\Optional(98),
        100 => new \Phplrt\Parser\Grammar\Alternation([103, 104]),
        101 => new \Phplrt\Parser\Grammar\Concatenation([107, 111]),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        103 => new \Phplrt\Parser\Grammar\Concatenation([102, 101]),
        104 => new \Phplrt\Parser\Grammar\Concatenation([101, 106]),
        105 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        106 => new \Phplrt\Parser\Grammar\Optional(105),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        109 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        110 => new \Phplrt\Parser\Grammar\Concatenation([108, 109]),
        111 => new \Phplrt\Parser\Grammar\Repetition(110, 0, INF),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        113 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        114 => new \Phplrt\Parser\Grammar\Concatenation([112, 26, 113]),
        107 => new \Phplrt\Parser\Grammar\Alternation([114, 6, 67, 89])
    ],
    'reducers' => [
        7 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        8 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        9 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
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
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $isSealed = \array_pop($children);
    
        if ($children === [] && $isSealed) {
            throw new Exception\LogicException('A shape without fields cannot be sealed', $offset);
        }
    
        return new Node\Stmt\Shape\FieldsListNode($children, $isSealed);
        },
        33 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        41 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        44 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        42 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return match(true) {
            $children instanceof Node\Literal\IntLiteralNode,
            $children instanceof Node\Literal\StringLiteralNode => $children,
            $children instanceof Node\Literal\BoolLiteralNode,
            $children instanceof Node\Literal\NullLiteralNode,
                => new Node\Literal\StringLiteralNode($children->raw),
            default => new Node\Literal\StringLiteralNode($children->getValue()),
        };
        },
        49 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\FullQualifiedName($parts);
        },
        50 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $parts = [];
    
        foreach ($children as $child) {
            $parts[] = $child->getValue();
        }
    
        return new Node\Name($parts);
        },
        67 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        61 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        68 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\NamedArgumentNode($children[1], $children[0]);
        },
        74 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        return new Node\Stmt\Callable\OutArgumentNode($children[0]);
        },
        76 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\Callable\VariadicArgumentNode(
                new Node\Stmt\Callable\ArgumentNode($children[1]),
            );
        }
    
        return $children;
        },
        81 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (!isset($children[1])) {
            return $argument;
        }
    
        if ($children[1]->getName() === 'T_EQ') {
            return new Node\Stmt\Callable\OptionalArgumentNode($argument);
        }
    
        return new Node\Stmt\Callable\VariadicArgumentNode($argument);
        },
        89 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        91 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        92 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        100 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        104 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Stmt\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        101 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];