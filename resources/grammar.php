<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\FeatureNotAllowedException;

return [
    'initial' => 49,
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
        40 => new \Phplrt\Parser\Grammar\Concatenation([49]),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        42 => new \Phplrt\Parser\Grammar\Concatenation([41, 40]),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        45 => new \Phplrt\Parser\Grammar\Repetition(42, 0, INF),
        46 => new \Phplrt\Parser\Grammar\Optional(43),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        48 => new \Phplrt\Parser\Grammar\Concatenation([44, 40, 45, 46, 47]),
        49 => new \Phplrt\Parser\Grammar\Concatenation([118]),
        50 => new \Phplrt\Parser\Grammar\Concatenation([57, 61, 62]),
        51 => new \Phplrt\Parser\Grammar\Concatenation([76, 49]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        53 => new \Phplrt\Parser\Grammar\Optional(50),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        55 => new \Phplrt\Parser\Grammar\Optional(51),
        56 => new \Phplrt\Parser\Grammar\Concatenation([2, 52, 53, 54, 55]),
        57 => new \Phplrt\Parser\Grammar\Concatenation([63, 65]),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        59 => new \Phplrt\Parser\Grammar\Concatenation([58, 57]),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        61 => new \Phplrt\Parser\Grammar\Repetition(59, 0, INF),
        62 => new \Phplrt\Parser\Grammar\Optional(60),
        63 => new \Phplrt\Parser\Grammar\Concatenation([66, 67]),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        65 => new \Phplrt\Parser\Grammar\Optional(64),
        66 => new \Phplrt\Parser\Grammar\Alternation([70, 73]),
        67 => new \Phplrt\Parser\Grammar\Optional(24),
        68 => new \Phplrt\Parser\Grammar\Concatenation([49, 75]),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        70 => new \Phplrt\Parser\Grammar\Concatenation([69, 68]),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        72 => new \Phplrt\Parser\Grammar\Optional(71),
        73 => new \Phplrt\Parser\Grammar\Concatenation([68, 72]),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        75 => new \Phplrt\Parser\Grammar\Optional(74),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        77 => new \Phplrt\Parser\Grammar\Concatenation([92, 95]),
        78 => new \Phplrt\Parser\Grammar\Concatenation([90, 91]),
        79 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        80 => new \Phplrt\Parser\Grammar\Concatenation([79, 78]),
        81 => new \Phplrt\Parser\Grammar\Optional(80),
        82 => new \Phplrt\Parser\Grammar\Concatenation([77, 81]),
        83 => new \Phplrt\Parser\Grammar\Optional(78),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        86 => new \Phplrt\Parser\Grammar\Alternation([82, 83]),
        87 => new \Phplrt\Parser\Grammar\Optional(84),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        89 => new \Phplrt\Parser\Grammar\Concatenation([85, 86, 87, 88]),
        90 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        91 => new \Phplrt\Parser\Grammar\Optional(48),
        92 => new \Phplrt\Parser\Grammar\Alternation([96, 97]),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        94 => new \Phplrt\Parser\Grammar\Concatenation([93, 92]),
        95 => new \Phplrt\Parser\Grammar\Repetition(94, 0, INF),
        96 => new \Phplrt\Parser\Grammar\Concatenation([98, 101, 102, 100]),
        97 => new \Phplrt\Parser\Grammar\Concatenation([100]),
        98 => new \Phplrt\Parser\Grammar\Alternation([3, 21, 19]),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        100 => new \Phplrt\Parser\Grammar\Concatenation([49]),
        101 => new \Phplrt\Parser\Grammar\Optional(99),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        103 => new \Phplrt\Parser\Grammar\Alternation([48, 89]),
        104 => new \Phplrt\Parser\Grammar\Optional(103),
        105 => new \Phplrt\Parser\Grammar\Concatenation([2, 104]),
        106 => new \Phplrt\Parser\Grammar\Concatenation([119]),
        107 => new \Phplrt\Parser\Grammar\Optional(109),
        108 => new \Phplrt\Parser\Grammar\Concatenation([106, 107]),
        109 => new \Phplrt\Parser\Grammar\Concatenation([114, 115, 116, 49, 117, 49]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([24, 109]),
        111 => new \Phplrt\Parser\Grammar\Alternation([108, 110]),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        113 => new \Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        114 => new \Phplrt\Parser\Grammar\Alternation([112, 113]),
        115 => new \Phplrt\Parser\Grammar\Alternation([24, 49]),
        116 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        118 => new \Phplrt\Parser\Grammar\Concatenation([111]),
        119 => new \Phplrt\Parser\Grammar\Concatenation([120, 123]),
        120 => new \Phplrt\Parser\Grammar\Concatenation([124, 127]),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        122 => new \Phplrt\Parser\Grammar\Concatenation([121, 119]),
        123 => new \Phplrt\Parser\Grammar\Optional(122),
        124 => new \Phplrt\Parser\Grammar\Concatenation([128]),
        125 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        126 => new \Phplrt\Parser\Grammar\Concatenation([125, 120]),
        127 => new \Phplrt\Parser\Grammar\Optional(126),
        128 => new \Phplrt\Parser\Grammar\Alternation([131, 129]),
        129 => new \Phplrt\Parser\Grammar\Concatenation([132, 136]),
        130 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        131 => new \Phplrt\Parser\Grammar\Concatenation([130, 129]),
        133 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        134 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        135 => new \Phplrt\Parser\Grammar\Concatenation([133, 134]),
        136 => new \Phplrt\Parser\Grammar\Repetition(135, 0, INF),
        137 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        138 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        139 => new \Phplrt\Parser\Grammar\Concatenation([137, 49, 138]),
        132 => new \Phplrt\Parser\Grammar\Alternation([139, 18, 56, 105])
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
        56 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        50 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ParametersListNode($children);
        },
        57 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if (!isset($children[1])) {
            return $children[0];
        }
    
        if ($children[0]->variadic) {
            throw SemanticException::fromVariadicWithDefault($offset);
        }
    
        $children[0]->optional = true;
        return $children[0];
        },
        63 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        66 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        68 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ParameterNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        89 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        77 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            $explicit = [];
        $implicit = false;
    
        foreach ($children as $field) {
            if ($field instanceof Node\Stmt\Shape\ExplicitFieldNode) {
                $key = $field->getKey();
    
                if (\in_array($key, $explicit, true)) {
                    throw SemanticException::fromShapeFieldDuplication($key, $field->offset);
                }
    
                $explicit[] = $key;
            } else {
                $implicit = true;
            }
        }
    
        if ($explicit !== [] && $implicit) {
            throw SemanticException::fromShapeMixedKeys($offset);
        }
    
        return new Node\Stmt\Shape\FieldsListNode($children);
        },
        96 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        97 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ImplicitFieldNode($children[0]);
        },
        105 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        111 => function (\Phplrt\Parser\Context $ctx, $children) {
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
            default => throw SemanticException::fromInvalidConditionalOperator(
                $children[1]->getValue(),
                $offset,
            ),
        };
    
        return new Node\Stmt\TernaryConditionNode(
            $condition,
            $children[3],
            $children[4],
        );
        },
        119 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        120 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        128 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        129 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];