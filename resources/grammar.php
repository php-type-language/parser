<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\FeatureNotAllowedException;

return [
    'initial' => 'Type',
    'tokens' => [
        'default' => [
            'T_DQ_STRING_LITERAL' => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL' => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_PFX_FLOAT_LITERAL' => '\\-?(?i)[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?',
            'T_SFX_FLOAT_LITERAL' => '\\-?(?i)[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?',
            'T_EXP_LITERAL' => '\\-?(?i)[0-9]++e-?[0-9]++',
            'T_BIN_INT_LITERAL' => '\\-?(?i)0b[0-1_]++',
            'T_OCT_INT_LITERAL' => '\\-?(?i)0o[0-7_]++',
            'T_HEX_INT_LITERAL' => '\\-?(?i)0x[0-9a-f_]++',
            'T_DEC_INT_LITERAL' => '\\-?(?i)[0-9][0-9_]*+',
            'T_BOOL_LITERAL' => '(?i)(?:true|false)(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NULL_LITERAL' => '(?i)(?:null)(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NEQ' => '(?i)is\\h+not(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_EQ' => '(?i)is(?![a-zA-Z0-9\\-_\\x80-\\xff])',
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
            'T_QMARK' => '\\?',
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
        3 => new \Phplrt\Parser\Grammar\Alternation([11, 12, 13, 14]),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        5 => new \Phplrt\Parser\Grammar\Concatenation([4, 3]),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        7 => new \Phplrt\Parser\Grammar\Repetition(5, 0, INF),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        9 => new \Phplrt\Parser\Grammar\Concatenation([8, 3]),
        10 => new \Phplrt\Parser\Grammar\Repetition(9, 0, INF),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        15 => new \Phplrt\Parser\Grammar\Alternation([19, 20, 21, 22, 23]),
        16 => new \Phplrt\Parser\Grammar\Concatenation([2, 34]),
        17 => new \Phplrt\Parser\Grammar\Concatenation([2, 38, 39]),
        18 => new \Phplrt\Parser\Grammar\Alternation([15, 16, 17]),
        19 => new \Phplrt\Parser\Grammar\Alternation([25, 26]),
        20 => new \Phplrt\Parser\Grammar\Alternation([27, 28, 29]),
        21 => new \Phplrt\Parser\Grammar\Alternation([30, 31, 32, 33]),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_PFX_FLOAT_LITERAL', true),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_SFX_FLOAT_LITERAL', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_EXP_LITERAL', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_BIN_INT_LITERAL', true),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_OCT_INT_LITERAL', true),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_HEX_INT_LITERAL', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_DEC_INT_LITERAL', true),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        36 => new \Phplrt\Parser\Grammar\Concatenation([3, 35]),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        39 => new \Phplrt\Parser\Grammar\Alternation([36, 3, 37]),
        40 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        42 => new \Phplrt\Parser\Grammar\Concatenation([41, 40]),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        45 => new \Phplrt\Parser\Grammar\Repetition(42, 0, INF),
        46 => new \Phplrt\Parser\Grammar\Optional(43),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        48 => new \Phplrt\Parser\Grammar\Concatenation([44, 40, 45, 46, 47]),
        49 => new \Phplrt\Parser\Grammar\Concatenation([56, 60, 61]),
        50 => new \Phplrt\Parser\Grammar\Concatenation([75, 'Type']),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        52 => new \Phplrt\Parser\Grammar\Optional(49),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        54 => new \Phplrt\Parser\Grammar\Optional(50),
        55 => new \Phplrt\Parser\Grammar\Concatenation([2, 51, 52, 53, 54]),
        56 => new \Phplrt\Parser\Grammar\Concatenation([62, 64]),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        58 => new \Phplrt\Parser\Grammar\Concatenation([57, 56]),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        60 => new \Phplrt\Parser\Grammar\Repetition(58, 0, INF),
        61 => new \Phplrt\Parser\Grammar\Optional(59),
        62 => new \Phplrt\Parser\Grammar\Concatenation([65, 66]),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        64 => new \Phplrt\Parser\Grammar\Optional(63),
        65 => new \Phplrt\Parser\Grammar\Alternation([69, 72]),
        66 => new \Phplrt\Parser\Grammar\Optional(24),
        67 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 74]),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        69 => new \Phplrt\Parser\Grammar\Concatenation([68, 67]),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        71 => new \Phplrt\Parser\Grammar\Optional(70),
        72 => new \Phplrt\Parser\Grammar\Concatenation([67, 71]),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        74 => new \Phplrt\Parser\Grammar\Optional(73),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        76 => new \Phplrt\Parser\Grammar\Concatenation([91, 94]),
        77 => new \Phplrt\Parser\Grammar\Concatenation([89, 90]),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        79 => new \Phplrt\Parser\Grammar\Concatenation([78, 77]),
        80 => new \Phplrt\Parser\Grammar\Optional(79),
        81 => new \Phplrt\Parser\Grammar\Concatenation([76, 80]),
        82 => new \Phplrt\Parser\Grammar\Optional(77),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        85 => new \Phplrt\Parser\Grammar\Alternation([81, 82]),
        86 => new \Phplrt\Parser\Grammar\Optional(83),
        87 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        88 => new \Phplrt\Parser\Grammar\Concatenation([84, 85, 86, 87]),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        90 => new \Phplrt\Parser\Grammar\Optional(48),
        91 => new \Phplrt\Parser\Grammar\Alternation([95, 96]),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        93 => new \Phplrt\Parser\Grammar\Concatenation([92, 91]),
        94 => new \Phplrt\Parser\Grammar\Repetition(93, 0, INF),
        95 => new \Phplrt\Parser\Grammar\Concatenation([97, 100, 101, 99]),
        96 => new \Phplrt\Parser\Grammar\Concatenation([99]),
        97 => new \Phplrt\Parser\Grammar\Alternation([3, 21, 19]),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        99 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        100 => new \Phplrt\Parser\Grammar\Optional(98),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        102 => new \Phplrt\Parser\Grammar\Alternation([48, 88]),
        103 => new \Phplrt\Parser\Grammar\Optional(102),
        104 => new \Phplrt\Parser\Grammar\Concatenation([2, 103]),
        105 => new \Phplrt\Parser\Grammar\Concatenation([118]),
        106 => new \Phplrt\Parser\Grammar\Optional(108),
        107 => new \Phplrt\Parser\Grammar\Concatenation([105, 106]),
        108 => new \Phplrt\Parser\Grammar\Concatenation([113, 114, 115, 'Type', 116, 'Type']),
        109 => new \Phplrt\Parser\Grammar\Concatenation([24, 108]),
        110 => new \Phplrt\Parser\Grammar\Alternation([107, 109]),
        111 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        113 => new \Phplrt\Parser\Grammar\Alternation([111, 112]),
        114 => new \Phplrt\Parser\Grammar\Alternation([24, 'Type']),
        115 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        116 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        117 => new \Phplrt\Parser\Grammar\Concatenation([110]),
        118 => new \Phplrt\Parser\Grammar\Concatenation([119, 122]),
        119 => new \Phplrt\Parser\Grammar\Concatenation([123, 126]),
        120 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        121 => new \Phplrt\Parser\Grammar\Concatenation([120, 118]),
        122 => new \Phplrt\Parser\Grammar\Optional(121),
        123 => new \Phplrt\Parser\Grammar\Concatenation([127]),
        124 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        125 => new \Phplrt\Parser\Grammar\Concatenation([124, 119]),
        126 => new \Phplrt\Parser\Grammar\Optional(125),
        127 => new \Phplrt\Parser\Grammar\Alternation([130, 128]),
        128 => new \Phplrt\Parser\Grammar\Concatenation([131, 135]),
        129 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        130 => new \Phplrt\Parser\Grammar\Concatenation([129, 128]),
        132 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        133 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        134 => new \Phplrt\Parser\Grammar\Concatenation([132, 133]),
        135 => new \Phplrt\Parser\Grammar\Repetition(134, 0, INF),
        136 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        137 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        138 => new \Phplrt\Parser\Grammar\Concatenation([136, 'Type', 137]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([117]),
        131 => new \Phplrt\Parser\Grammar\Alternation([138, 18, 55, 104])
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
        15 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($this->literals === false) {
            throw FeatureNotAllowedException::fromFeature('literal values', $offset);
        }
        return $children;
        },
        24 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->stringPool[$token] ??= $children;
        },
        25 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        26 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        20 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        21 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        22 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        23 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        16 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        48 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        40 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        55 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $name = \array_shift($children);
    
        if ($this->callables === false) {
            throw FeatureNotAllowedException::fromFeature('callable types', $offset);
        }
    
        $parameters = isset($children[0]) && $children[0] instanceof Node\Stmt\Callable\ParametersListNode
            ? \array_shift($children)
            : new Node\Stmt\Callable\ParametersListNode();
    
        return new Node\Stmt\CallableTypeNode(
            name: $name,
            parameters: $parameters,
            type: isset($children[0]) ? $children[0] : null,
        );
        },
        49 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ParametersListNode($children);
        },
        56 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        62 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        65 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0] instanceof Node\Stmt\Callable\ParameterNode) {
            $children[0]->variadic = true;
            return $children[0];
        }
    
        $children[1]->variadic = true;
        return $children[1];
        },
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ParameterNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        88 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($children === []) {
            return new Node\Stmt\Shape\FieldsListNode();
        }
    
        if ($this->shapes === false) {
            throw FeatureNotAllowedException::fromFeature('shape fields', $offset);
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
        76 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $explicit = [];
        $implicit = false;
    
        foreach ($children as $field) {
            if ($field instanceof Node\Stmt\Shape\ExplicitFieldNode) {
                $key = $field->getKey();
    
                if (\in_array($key, $explicit, true)) {
                    throw new SemanticException(
                        \sprintf('Duplicate key "%s"', $key),
                        $field->offset,
                        SemanticException::CODE_SHAPE_KEY_DUPLICATION,
                    );
                }
    
                $explicit[] = $key;
            } else {
                $implicit = true;
            }
        }
    
        if ($explicit !== [] && $implicit) {
            throw new SemanticException(
                \sprintf('Cannot mix explicit and implicit shape keys', $key),
                $offset,
                SemanticException::CODE_SHAPE_KEY_MIX,
            );
        }
    
        return new Node\Stmt\Shape\FieldsListNode($children);
        },
        95 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        96 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ImplicitFieldNode($children[0]);
        },
        104 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        110 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $count = \count($children);
    
        if ($count === 1) {
            return $children[0];
        }
    
        if ($this->conditional === false) {
            throw FeatureNotAllowedException::fromFeature('conditional expressions', $offset);
        }
    
        $condition = match ($children[1]->getName()) {
            'T_EQ' => new Node\Stmt\Condition\EqualConditionNode(
                $children[0],
                $children[2],
            ),
            'T_NEQ' => new Node\Stmt\Condition\NotEqualConditionNode(
                $children[0],
                $children[2],
            ),
            default => throw new SemanticException(
                \sprintf('Invalid conditional operator "%s"', $children[1]->getValue()),
                $offset,
                SemanticException::CODE_INVALID_OPERATOR,
            ),
        };
    
        return new Node\Stmt\TernaryConditionNode(
            $condition,
            $children[3],
            $children[4],
        );
        },
        118 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        119 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        127 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        128 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];