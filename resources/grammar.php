<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;

return [
    'initial' => 'Document',
    'tokens' => [
        'default' => [
            'T_DQ_STRING_LITERAL' => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL' => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_FLOAT_LITERAL' => '(?i)(?:-?[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?)|(?:-?[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?)|(?:-?[0-9]++e-?[0-9]++)',
            'T_INT_LITERAL' => '\\-?(?i)(?:(?:0b[0-1_]++)|(?:0o[0-7_]++)|(?:0x[0-9a-f_]++)|(?:[0-9][0-9_]*+))',
            'T_BOOL_LITERAL' => '(?i)(?:true|false)\\b',
            'T_NULL_LITERAL' => '(?i)(?:null)\\b',
            'T_TYPE' => 'type\\b',
            'T_OUT' => 'out\\b',
            'T_IN' => 'in\\b',
            'T_VARIABLE' => '\\$[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_ANGLE_BRACKET_OPEN' => '<',
            'T_ANGLE_BRACKET_CLOSE' => '>',
            'T_PARENTHESIS_OPEN' => '\\(',
            'T_PARENTHESIS_CLOSE' => '\\)',
            'T_BRACE_OPEN' => '{',
            'T_BRACE_CLOSE' => '}',
            'T_SQUARE_BRACKET_OPEN' => '\\[',
            'T_SQUARE_BRACKET_CLOSE' => '\\]',
            'T_COMMA' => ',',
            'T_ELLIPSIS' => '\\.\\.\\.',
            'T_SEMICOLON' => ';',
            'T_DOUBLE_COLON' => '::',
            'T_COLON' => ':',
            'T_ASSIGN' => '=',
            'T_NS_DELIMITER' => '\\\\',
            'T_NULLABLE' => '\\?',
            'T_NOT' => '\\!',
            'T_OR' => '\\|',
            'T_AMP' => '&',
            'T_ASTERISK' => '\\*',
            'T_COMMENT' => '(//|#).+?$',
            'T_DOC_COMMENT' => '/\\*.*?\\*/',
            'T_WHITESPACE' => '(\\xfe\\xff|\\x20|\\x09|\\x0a|\\x0d)+',
        ],
    ],
    'skip' => [
        'T_COMMENT',
        'T_DOC_COMMENT',
        'T_WHITESPACE',
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
        13 => new \Phplrt\Parser\Grammar\Alternation([28, 29, 30, 31, 32, 33]),
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
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', true),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_OUT', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_IN', true),
        34 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        36 => new \Phplrt\Parser\Grammar\Concatenation([35, 34]),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        39 => new \Phplrt\Parser\Grammar\Repetition(36, 0, INF),
        40 => new \Phplrt\Parser\Grammar\Optional(37),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        42 => new \Phplrt\Parser\Grammar\Concatenation([38, 34, 39, 40, 41]),
        43 => new \Phplrt\Parser\Grammar\Concatenation([57, 60]),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        46 => new \Phplrt\Parser\Grammar\Concatenation([44, 45]),
        47 => new \Phplrt\Parser\Grammar\Optional(46),
        48 => new \Phplrt\Parser\Grammar\Concatenation([43, 47]),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        50 => new \Phplrt\Parser\Grammar\Optional(49),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        53 => new \Phplrt\Parser\Grammar\Alternation([48, 50]),
        54 => new \Phplrt\Parser\Grammar\Optional(51),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        56 => new \Phplrt\Parser\Grammar\Concatenation([52, 53, 54, 55]),
        57 => new \Phplrt\Parser\Grammar\Alternation([61, 62]),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        59 => new \Phplrt\Parser\Grammar\Concatenation([58, 57]),
        60 => new \Phplrt\Parser\Grammar\Repetition(59, 0, INF),
        61 => new \Phplrt\Parser\Grammar\Concatenation([63, 66, 67, 65]),
        62 => new \Phplrt\Parser\Grammar\Concatenation([65]),
        63 => new \Phplrt\Parser\Grammar\Alternation([13, 2, 0]),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        65 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        66 => new \Phplrt\Parser\Grammar\Optional(64),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        68 => new \Phplrt\Parser\Grammar\Concatenation([75, 79, 80]),
        69 => new \Phplrt\Parser\Grammar\Concatenation([94, 'Type']),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        71 => new \Phplrt\Parser\Grammar\Optional(68),
        72 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        73 => new \Phplrt\Parser\Grammar\Optional(69),
        74 => new \Phplrt\Parser\Grammar\Concatenation([11, 70, 71, 72, 73]),
        75 => new \Phplrt\Parser\Grammar\Concatenation([81, 83]),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        77 => new \Phplrt\Parser\Grammar\Concatenation([76, 75]),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        79 => new \Phplrt\Parser\Grammar\Repetition(77, 0, INF),
        80 => new \Phplrt\Parser\Grammar\Optional(78),
        81 => new \Phplrt\Parser\Grammar\Concatenation([84, 85]),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        83 => new \Phplrt\Parser\Grammar\Optional(82),
        84 => new \Phplrt\Parser\Grammar\Alternation([88, 91]),
        85 => new \Phplrt\Parser\Grammar\Optional(8),
        86 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 93]),
        87 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        88 => new \Phplrt\Parser\Grammar\Concatenation([87, 86]),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        90 => new \Phplrt\Parser\Grammar\Optional(89),
        91 => new \Phplrt\Parser\Grammar\Concatenation([86, 90]),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        93 => new \Phplrt\Parser\Grammar\Optional(92),
        94 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        95 => new \Phplrt\Parser\Grammar\Optional(42),
        96 => new \Phplrt\Parser\Grammar\Optional(56),
        97 => new \Phplrt\Parser\Grammar\Concatenation([11, 95, 96]),
        98 => new \Phplrt\Parser\Grammar\Concatenation([105, 13, 107]),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        100 => new \Phplrt\Parser\Grammar\Concatenation([99, 98]),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        102 => new \Phplrt\Parser\Grammar\Repetition(100, 0, INF),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        104 => new \Phplrt\Parser\Grammar\Concatenation([101, 98, 102, 103]),
        105 => new \Phplrt\Parser\Grammar\Optional(111),
        106 => new \Phplrt\Parser\Grammar\Concatenation([108, 'Type']),
        107 => new \Phplrt\Parser\Grammar\Optional(106),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        109 => new \Phplrt\Parser\Grammar\Lexeme('T_IN', true),
        110 => new \Phplrt\Parser\Grammar\Lexeme('T_OUT', true),
        111 => new \Phplrt\Parser\Grammar\Alternation([109, 110]),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', false),
        113 => new \Phplrt\Parser\Grammar\Concatenation([112, 'Type']),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        115 => new \Phplrt\Parser\Grammar\Optional(104),
        116 => new \Phplrt\Parser\Grammar\Optional(113),
        117 => new \Phplrt\Parser\Grammar\Concatenation([114, 13, 115, 116]),
        118 => new \Phplrt\Parser\Grammar\Concatenation([117, 119]),
        119 => new \Phplrt\Parser\Grammar\Lexeme('T_SEMICOLON', false),
        120 => new \Phplrt\Parser\Grammar\Concatenation([121]),
        121 => new \Phplrt\Parser\Grammar\Concatenation([122, 125]),
        122 => new \Phplrt\Parser\Grammar\Concatenation([126, 129]),
        123 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        124 => new \Phplrt\Parser\Grammar\Concatenation([123, 121]),
        125 => new \Phplrt\Parser\Grammar\Optional(124),
        126 => new \Phplrt\Parser\Grammar\Concatenation([130]),
        127 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        128 => new \Phplrt\Parser\Grammar\Concatenation([127, 122]),
        129 => new \Phplrt\Parser\Grammar\Optional(128),
        130 => new \Phplrt\Parser\Grammar\Alternation([133, 134]),
        131 => new \Phplrt\Parser\Grammar\Concatenation([137, 141]),
        132 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        133 => new \Phplrt\Parser\Grammar\Concatenation([132, 131]),
        134 => new \Phplrt\Parser\Grammar\Concatenation([131, 136]),
        135 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        136 => new \Phplrt\Parser\Grammar\Optional(135),
        138 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        139 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        140 => new \Phplrt\Parser\Grammar\Concatenation([138, 139]),
        141 => new \Phplrt\Parser\Grammar\Repetition(140, 0, INF),
        142 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        143 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        144 => new \Phplrt\Parser\Grammar\Concatenation([142, 'Type', 143]),
        'Document' => new \Phplrt\Parser\Grammar\Repetition(118, 1, INF),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([120]),
        137 => new \Phplrt\Parser\Grammar\Alternation([144, 7, 74, 97])
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
        42 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Template\ParametersListNode($children);
        },
        34 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        43 => function (\Phplrt\Parser\Context $ctx, $children) {
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
                        SemanticException::CODE_SHAPE_KEY_DUPLICATION,
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
                SemanticException::CODE_SHAPE_KEY_MIX,
            );
        }
    
        return new \ArrayObject($children);
        },
        61 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        62 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Shape\FieldNode($children[0]);
        },
        74 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        68 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Type\Callable\ArgumentsListNode($children);
        },
        75 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0]->variadic) {
            throw new SemanticException(
                'Cannot have variadic param with a default',
                $offset,
                SemanticException::CODE_VARIADIC_WITH_DEFAULT,
            );
        }
    
        $children[0]->optional = true;
        return $children[0];
        },
        81 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        84 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        86 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Type\Callable\ArgumentNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        97 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        104 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Definition\Template\ParametersListNode($children);
        },
        98 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Definition\Template\ParameterNode(
            $children[1],
            $children[0],
            $children[2] ?? null,
        );
        },
        105 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($children === []) {
            return Node\Definition\Template\ParameterVariance::ANY;
        }
    
        return match ($children->getValue()) {
            'in' => Node\Definition\Template\ParameterVariance::IN,
            'out' => Node\Definition\Template\ParameterVariance::OUT,
            default => throw new SemanticException(
                \sprintf('Unprocessable type variance "%s"', $children->getValue()),
                $offset,
            ),
        };
        },
        117 => function (\Phplrt\Parser\Context $ctx, $children) {
            $type = $parameters = null;
    
        if (\end($children) instanceof Node\Type\TypeStatement) {
            $type = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Definition\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Definition\TypeDefinitionNode($children[0], $type, $parameters);
        },
        121 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Type\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        122 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Type\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        130 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Type\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        134 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Type\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        131 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Type\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];