<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;

return [
    'initial' => 'Type',
    'tokens' => [
        'default' => [
            'T_DQ_STRING_LITERAL' => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL' => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_FLOAT_LITERAL' => '(?i)(?:-?[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?)|(?:-?[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?)|(?:-?[0-9]++e-?[0-9]++)',
            'T_INT_LITERAL' => '\\-?(?i)(?:(?:0b[0-1_]++)|(?:0o[0-7_]++)|(?:0x[0-9a-f_]++)|(?:[0-9][0-9_]*+))',
            'T_BOOL_LITERAL' => '(?i)(?:true|false)(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NULL_LITERAL' => '(?i)(?:null)(?![a-zA-Z0-9\\-_\\x80-\\xff])',
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
        0 => new \Phplrt\Parser\Grammar\Concatenation([6, 3, 7]),
        1 => new \Phplrt\Parser\Grammar\Concatenation([3, 10]),
        2 => new \Phplrt\Parser\Grammar\Alternation([0, 1]),
        3 => new \Phplrt\Parser\Grammar\Alternation([11, 12, 13]),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        5 => new \Phplrt\Parser\Grammar\Concatenation([4, 3]),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        7 => new \Phplrt\Parser\Grammar\Repetition(5, 0, INF),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        9 => new \Phplrt\Parser\Grammar\Concatenation([8, 3]),
        10 => new \Phplrt\Parser\Grammar\Repetition(9, 0, INF),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        14 => new \Phplrt\Parser\Grammar\Alternation([23, 24]),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        19 => new \Phplrt\Parser\Grammar\Concatenation([2, 25]),
        20 => new \Phplrt\Parser\Grammar\Concatenation([2, 29, 30]),
        21 => new \Phplrt\Parser\Grammar\Alternation([14, 15, 16, 17, 18, 19, 20]),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        27 => new \Phplrt\Parser\Grammar\Concatenation([3, 26]),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        30 => new \Phplrt\Parser\Grammar\Alternation([27, 3, 28]),
        31 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        33 => new \Phplrt\Parser\Grammar\Concatenation([32, 31]),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        36 => new \Phplrt\Parser\Grammar\Repetition(33, 0, INF),
        37 => new \Phplrt\Parser\Grammar\Optional(34),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        39 => new \Phplrt\Parser\Grammar\Concatenation([35, 31, 36, 37, 38]),
        40 => new \Phplrt\Parser\Grammar\Concatenation([47, 51, 52]),
        41 => new \Phplrt\Parser\Grammar\Concatenation([66, 'Type']),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        43 => new \Phplrt\Parser\Grammar\Optional(40),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        45 => new \Phplrt\Parser\Grammar\Optional(41),
        46 => new \Phplrt\Parser\Grammar\Concatenation([2, 42, 43, 44, 45]),
        47 => new \Phplrt\Parser\Grammar\Concatenation([53, 55]),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        49 => new \Phplrt\Parser\Grammar\Concatenation([48, 47]),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        51 => new \Phplrt\Parser\Grammar\Repetition(49, 0, INF),
        52 => new \Phplrt\Parser\Grammar\Optional(50),
        53 => new \Phplrt\Parser\Grammar\Concatenation([56, 57]),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        55 => new \Phplrt\Parser\Grammar\Optional(54),
        56 => new \Phplrt\Parser\Grammar\Alternation([60, 63]),
        57 => new \Phplrt\Parser\Grammar\Optional(22),
        58 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 65]),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        60 => new \Phplrt\Parser\Grammar\Concatenation([59, 58]),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        62 => new \Phplrt\Parser\Grammar\Optional(61),
        63 => new \Phplrt\Parser\Grammar\Concatenation([58, 62]),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        65 => new \Phplrt\Parser\Grammar\Optional(64),
        66 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        67 => new \Phplrt\Parser\Grammar\Concatenation([82, 85]),
        68 => new \Phplrt\Parser\Grammar\Concatenation([80, 81]),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        70 => new \Phplrt\Parser\Grammar\Concatenation([69, 68]),
        71 => new \Phplrt\Parser\Grammar\Optional(70),
        72 => new \Phplrt\Parser\Grammar\Concatenation([67, 71]),
        73 => new \Phplrt\Parser\Grammar\Optional(68),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        76 => new \Phplrt\Parser\Grammar\Alternation([72, 73]),
        77 => new \Phplrt\Parser\Grammar\Optional(74),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        79 => new \Phplrt\Parser\Grammar\Concatenation([75, 76, 77, 78]),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        81 => new \Phplrt\Parser\Grammar\Optional(39),
        82 => new \Phplrt\Parser\Grammar\Alternation([86, 87]),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        84 => new \Phplrt\Parser\Grammar\Concatenation([83, 82]),
        85 => new \Phplrt\Parser\Grammar\Repetition(84, 0, INF),
        86 => new \Phplrt\Parser\Grammar\Concatenation([88, 91, 92, 90]),
        87 => new \Phplrt\Parser\Grammar\Concatenation([90]),
        88 => new \Phplrt\Parser\Grammar\Alternation([3, 16, 14]),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        90 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        91 => new \Phplrt\Parser\Grammar\Optional(89),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        93 => new \Phplrt\Parser\Grammar\Alternation([39, 79]),
        94 => new \Phplrt\Parser\Grammar\Optional(93),
        95 => new \Phplrt\Parser\Grammar\Concatenation([2, 94]),
        96 => new \Phplrt\Parser\Grammar\Concatenation([97]),
        97 => new \Phplrt\Parser\Grammar\Concatenation([98, 101]),
        98 => new \Phplrt\Parser\Grammar\Concatenation([102, 105]),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        100 => new \Phplrt\Parser\Grammar\Concatenation([99, 97]),
        101 => new \Phplrt\Parser\Grammar\Optional(100),
        102 => new \Phplrt\Parser\Grammar\Concatenation([106]),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        104 => new \Phplrt\Parser\Grammar\Concatenation([103, 98]),
        105 => new \Phplrt\Parser\Grammar\Optional(104),
        106 => new \Phplrt\Parser\Grammar\Alternation([109, 110]),
        107 => new \Phplrt\Parser\Grammar\Concatenation([113, 117]),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        109 => new \Phplrt\Parser\Grammar\Concatenation([108, 107]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([107, 112]),
        111 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        112 => new \Phplrt\Parser\Grammar\Optional(111),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        115 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        116 => new \Phplrt\Parser\Grammar\Concatenation([114, 115]),
        117 => new \Phplrt\Parser\Grammar\Repetition(116, 0, INF),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        119 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        120 => new \Phplrt\Parser\Grammar\Concatenation([118, 'Type', 119]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([96]),
        113 => new \Phplrt\Parser\Grammar\Alternation([120, 21, 46, 95])
    ],
    'reducers' => [
        0 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\FullQualifiedName($children);
        },
        1 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Name($children);
        },
        3 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Identifier($children->getValue());
        },
        22 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        14 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->stringPool[$token] ??= $children;
        },
        23 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        24 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        15 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        16 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        18 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        20 => function (\Phplrt\Parser\Context $ctx, $children) {
            // <ClassName> :: <ConstPrefix> "*"
        if (\count($children) === 3) {
            return new Node\Stmt\ClassConstMaskNode(
                $children[0],
                $children[1],
            );
        }
    
        // <ClassName> :: <ConstName>
        if ($children[1] instanceof Node\Identifier) {
            return new Node\Stmt\ClassConstNode(
                $children[0],
                $children[1],
            );
        }
    
        // <ClassName> :: "*"
        return new Node\Stmt\ClassConstMaskNode($children[0]);
        },
        39 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        31 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        46 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        40 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        47 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        53 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        56 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0] instanceof Node\Stmt\Callable\ArgumentNode) {
            $children[0]->variadic = true;
            return $children[0];
        }
    
        $children[1]->variadic = true;
        return $children[1];
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        79 => function (\Phplrt\Parser\Context $ctx, $children) {
            if ($children === []) {
            return new Node\Stmt\Shape\FieldsListNode();
        }
    
        $parameters = null;
    
        if (\end($children) instanceof Node\Stmt\Template\ArgumentsListNode) {
            $parameters = \array_pop($children);
        }
    
        $fields = \reset($children) instanceof Node\Stmt\Shape\FieldsListNode
            ? \array_shift($children)
            : new Node\Stmt\Shape\FieldsListNode();
    
        if ($children !== []) {
            $fields->sealed = false;
        }
    
        return \array_filter([$parameters, $fields]);
        },
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $explicit = [];
        $implicit = false;
    
        foreach ($children as $field) {
            if ($field instanceof Node\Stmt\Shape\ExplicitFieldNode) {
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
    
        return new Node\Stmt\Shape\FieldsListNode($children);
        },
        86 => function (\Phplrt\Parser\Context $ctx, $children) {
            $name = $children[0];
        $value = \array_pop($children);
    
        // In case of "nullable" suffix defined
        $optional = \count($children) === 2;
    
        return match (true) {
            $name instanceof Node\Literal\IntLiteralNode
                => new Node\Stmt\Shape\NumericFieldNode($name, $value, $optional),
            $name instanceof Node\Literal\StringLiteralNode
                => new Node\Stmt\Shape\StringNamedFieldNode($name, $value, $optional),
            default => new Node\Stmt\Shape\NamedFieldNode($name, $value, $optional),
        };
        },
        87 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        95 => function (\Phplrt\Parser\Context $ctx, $children) {
            $fields = $parameters = null;
    
        // Shape fields
        if (\end($children) instanceof Node\Stmt\Shape\FieldsListNode) {
            $fields = \array_pop($children);
        }
    
        // Template parameters
        if (\end($children) instanceof Node\Stmt\Template\ArgumentsListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\NamedTypeNode(
            $children[0],
            $parameters,
            $fields,
        );
        },
        97 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        98 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        106 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        110 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Stmt\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        107 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];