<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;

return [
    'initial' => 'Statement',
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
        11 => new \Phplrt\Parser\Grammar\Alternation([19, 20]),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        13 => new \Phplrt\Parser\Grammar\Alternation([28, 29, 30]),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        15 => new \Phplrt\Parser\Grammar\Concatenation([13, 14]),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        18 => new \Phplrt\Parser\Grammar\Alternation([15, 13, 16]),
        19 => new \Phplrt\Parser\Grammar\Concatenation([23, 13, 24]),
        20 => new \Phplrt\Parser\Grammar\Concatenation([13, 27]),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        22 => new \Phplrt\Parser\Grammar\Concatenation([21, 13]),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        24 => new \Phplrt\Parser\Grammar\Repetition(22, 0, INF),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        26 => new \Phplrt\Parser\Grammar\Concatenation([25, 13]),
        27 => new \Phplrt\Parser\Grammar\Repetition(26, 0, INF),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        31 => new \Phplrt\Parser\Grammar\Concatenation(['TypeStatement']),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        33 => new \Phplrt\Parser\Grammar\Concatenation([32, 31]),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        36 => new \Phplrt\Parser\Grammar\Repetition(33, 0, INF),
        37 => new \Phplrt\Parser\Grammar\Optional(34),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        39 => new \Phplrt\Parser\Grammar\Concatenation([35, 31, 36, 37, 38]),
        40 => new \Phplrt\Parser\Grammar\Concatenation([59, 63, 64]),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        43 => new \Phplrt\Parser\Grammar\Concatenation([41, 42]),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        45 => new \Phplrt\Parser\Grammar\Optional(43),
        46 => new \Phplrt\Parser\Grammar\Optional(44),
        47 => new \Phplrt\Parser\Grammar\Concatenation([40, 45, 46]),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        50 => new \Phplrt\Parser\Grammar\Optional(48),
        51 => new \Phplrt\Parser\Grammar\Concatenation([49, 50]),
        52 => new \Phplrt\Parser\Grammar\Alternation([47, 51]),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        54 => new \Phplrt\Parser\Grammar\Optional(52),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        56 => new \Phplrt\Parser\Grammar\Concatenation([53, 54, 55]),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        58 => new \Phplrt\Parser\Grammar\Optional(57),
        59 => new \Phplrt\Parser\Grammar\Alternation([65, 66]),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        61 => new \Phplrt\Parser\Grammar\Concatenation([60, 59]),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        63 => new \Phplrt\Parser\Grammar\Repetition(61, 0, INF),
        64 => new \Phplrt\Parser\Grammar\Optional(62),
        65 => new \Phplrt\Parser\Grammar\Concatenation([67, 70, 71, 69]),
        66 => new \Phplrt\Parser\Grammar\Concatenation([69]),
        67 => new \Phplrt\Parser\Grammar\Alternation([13, 2, 0]),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        69 => new \Phplrt\Parser\Grammar\Concatenation(['TypeStatement']),
        70 => new \Phplrt\Parser\Grammar\Optional(68),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        72 => new \Phplrt\Parser\Grammar\Concatenation([79, 83, 84]),
        73 => new \Phplrt\Parser\Grammar\Concatenation([98, 'TypeStatement']),
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
        88 => new \Phplrt\Parser\Grammar\Alternation([92, 95]),
        89 => new \Phplrt\Parser\Grammar\Optional(8),
        90 => new \Phplrt\Parser\Grammar\Concatenation(['TypeStatement', 97]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        92 => new \Phplrt\Parser\Grammar\Concatenation([91, 90]),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        94 => new \Phplrt\Parser\Grammar\Optional(93),
        95 => new \Phplrt\Parser\Grammar\Concatenation([90, 94]),
        96 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        97 => new \Phplrt\Parser\Grammar\Optional(96),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        99 => new \Phplrt\Parser\Grammar\Optional(39),
        100 => new \Phplrt\Parser\Grammar\Optional(56),
        101 => new \Phplrt\Parser\Grammar\Concatenation([11, 99, 100]),
        102 => new \Phplrt\Parser\Grammar\Concatenation([103]),
        103 => new \Phplrt\Parser\Grammar\Concatenation([104, 107]),
        104 => new \Phplrt\Parser\Grammar\Concatenation([108, 111]),
        105 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        106 => new \Phplrt\Parser\Grammar\Concatenation([105, 103]),
        107 => new \Phplrt\Parser\Grammar\Optional(106),
        108 => new \Phplrt\Parser\Grammar\Concatenation([112]),
        109 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        110 => new \Phplrt\Parser\Grammar\Concatenation([109, 104]),
        111 => new \Phplrt\Parser\Grammar\Optional(110),
        112 => new \Phplrt\Parser\Grammar\Alternation([115, 116]),
        113 => new \Phplrt\Parser\Grammar\Concatenation([119, 123]),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        115 => new \Phplrt\Parser\Grammar\Concatenation([114, 113]),
        116 => new \Phplrt\Parser\Grammar\Concatenation([113, 118]),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        118 => new \Phplrt\Parser\Grammar\Optional(117),
        120 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        122 => new \Phplrt\Parser\Grammar\Concatenation([120, 121]),
        123 => new \Phplrt\Parser\Grammar\Repetition(122, 0, INF),
        124 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        125 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        126 => new \Phplrt\Parser\Grammar\Concatenation([124, 'TypeStatement', 125]),
        'TypeStatement' => new \Phplrt\Parser\Grammar\Concatenation([102]),
        119 => new \Phplrt\Parser\Grammar\Alternation([126, 7, 78, 101])
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
            return new Node\Type\ConstMaskNode($children[0]);
        },
        6 => function (\Phplrt\Parser\Context $ctx, $children) {
            // <ClassName> :: <ConstPrefix> "*"
        if (\count($children) === 3) {
            return new Node\Type\ClassConstMaskNode(
                $children[0],
                $children[1],
            );
        }
    
        // <ClassName> :: <ConstName>
        if ($children[1] instanceof Node\Identifier) {
            return new Node\Type\ClassConstNode(
                $children[0],
                $children[1],
            );
        }
    
        // <ClassName> :: "*"
        return new Node\Type\ClassConstMaskNode($children[0]);
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\FullQualifiedName($children);
        },
        20 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Name($children);
        },
        13 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Identifier($children->getValue());
        },
        39 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Template\ParametersListNode($children);
        },
        31 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Template\ParameterNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        56 => function (\Phplrt\Parser\Context $ctx, $children) {
            if ($children === []) {
            return new Node\Type\Shape\FieldsListNode();
        }
    
        if (!$children[0] instanceof \ArrayObject) {
            return new Node\Type\Shape\FieldsListNode([], false);
        }
    
        return new Node\Type\Shape\FieldsListNode(
            $children[0]->getArrayCopy(),
            \count($children) !== 2,
        );
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
            return $children === [];
        },
        40 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $explicit = [];
        $implicit = false;
    
        foreach ($children as $field) {
            if ($field instanceof Node\Type\Shape\ExplicitFieldNode) {
                $identifier = $field->getIdentifier();
    
                if (\in_array($identifier, $explicit, true)) {
                    throw new SemanticException(
                        \sprintf('Duplicate key "%s"', $identifier),
                        $field->offset,
                    );
                }
    
                $explicit[] = $identifier;
            } else {
                $implicit = true;
            }
        }
    
        if ($explicit !== [] && $implicit) {
            throw new SemanticException(
                \sprintf('Cannot mix explicit and implicit shape keys', $identifier),
                $offset,
            );
        }
    
        return new \ArrayObject($children);
        },
        65 => function (\Phplrt\Parser\Context $ctx, $children) {
            $name = $children[0];
        $value = \array_pop($children);
    
        // In case of "nullable" suffix defined
        $optional = \count($children) === 2;
    
        return match (true) {
            $name instanceof Node\Literal\IntLiteralNode
                => new Node\Type\Shape\NumericFieldNode($name, $value, $optional),
            $name instanceof Node\Literal\StringLiteralNode
                => new Node\Type\Shape\StringNamedFieldNode($name, $value, $optional),
            default => new Node\Type\Shape\NamedFieldNode($name, $value, $optional),
        };
        },
        66 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Shape\FieldNode($children[0]);
        },
        78 => function (\Phplrt\Parser\Context $ctx, $children) {
            $name = \array_shift($children);
    
        $arguments = isset($children[0]) && $children[0] instanceof Node\Type\Callable\ArgumentsListNode
            ? \array_shift($children)
            : new Node\Type\Callable\ArgumentsListNode();
    
        return new Node\Type\CallableTypeNode(
            name: $name,
            arguments: $arguments,
            type: isset($children[0]) ? $children[0] : null,
        );
        },
        72 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Callable\ArgumentsListNode($children);
        },
        79 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0]->variadic) {
            throw new SemanticException('Cannot have variadic param with a default', $offset);
        }
    
        $children[0]->optional = true;
        return $children[0];
        },
        85 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        88 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0] instanceof Node\Type\Callable\ArgumentNode) {
            $children[0]->variadic = true;
            return $children[0];
        }
    
        $children[1]->variadic = true;
        return $children[1];
        },
        90 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Type\Callable\ArgumentNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        101 => function (\Phplrt\Parser\Context $ctx, $children) {
            $fields = $parameters = null;
    
        // Shape fields
        if (\end($children) instanceof Node\Type\Shape\FieldsListNode) {
            $fields = \array_pop($children);
        }
    
        // Template parameters
        if (\end($children) instanceof Node\Type\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Type\NamedTypeNode(
            $children[0],
            $parameters,
            $fields,
        );
        },
        103 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Type\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        104 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Type\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        112 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Type\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        116 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Type\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        113 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Type\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];