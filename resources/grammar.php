<?php

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\FeatureNotAllowedException;

return [
    'initial' => 53,
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
            'T_NAME_WITH_SPACE' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*\\s+?',
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
        3 => new \Phplrt\Parser\Grammar\Alternation([11, 12, 13, 14, 15]),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        5 => new \Phplrt\Parser\Grammar\Concatenation([4, 3]),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        7 => new \Phplrt\Parser\Grammar\Repetition(5, 0, INF),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        9 => new \Phplrt\Parser\Grammar\Concatenation([8, 3]),
        10 => new \Phplrt\Parser\Grammar\Repetition(9, 0, INF),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        17 => new \Phplrt\Parser\Grammar\Alternation([21, 22, 23, 24, 25]),
        18 => new \Phplrt\Parser\Grammar\Concatenation([2, 36]),
        19 => new \Phplrt\Parser\Grammar\Concatenation([2, 40, 41]),
        20 => new \Phplrt\Parser\Grammar\Alternation([17, 18, 19]),
        21 => new \Phplrt\Parser\Grammar\Alternation([27, 28]),
        22 => new \Phplrt\Parser\Grammar\Alternation([29, 30, 31]),
        23 => new \Phplrt\Parser\Grammar\Alternation([32, 33, 34, 35]),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_PFX_FLOAT_LITERAL', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_SFX_FLOAT_LITERAL', true),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_EXP_LITERAL', true),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_BIN_INT_LITERAL', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_OCT_INT_LITERAL', true),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_HEX_INT_LITERAL', true),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_DEC_INT_LITERAL', true),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        38 => new \Phplrt\Parser\Grammar\Concatenation([3, 37]),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        41 => new \Phplrt\Parser\Grammar\Alternation([38, 3, 39]),
        42 => new \Phplrt\Parser\Grammar\Alternation([51, 52]),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        44 => new \Phplrt\Parser\Grammar\Concatenation([43, 42]),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        47 => new \Phplrt\Parser\Grammar\Repetition(44, 0, INF),
        48 => new \Phplrt\Parser\Grammar\Optional(45),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        50 => new \Phplrt\Parser\Grammar\Concatenation([46, 42, 47, 48, 49]),
        51 => new \Phplrt\Parser\Grammar\Concatenation([16, 53]),
        52 => new \Phplrt\Parser\Grammar\Concatenation([53]),
        53 => new \Phplrt\Parser\Grammar\Concatenation([122]),
        54 => new \Phplrt\Parser\Grammar\Concatenation([61, 65, 66]),
        55 => new \Phplrt\Parser\Grammar\Concatenation([80, 53]),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        57 => new \Phplrt\Parser\Grammar\Optional(54),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        59 => new \Phplrt\Parser\Grammar\Optional(55),
        60 => new \Phplrt\Parser\Grammar\Concatenation([2, 56, 57, 58, 59]),
        61 => new \Phplrt\Parser\Grammar\Concatenation([67, 69]),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        63 => new \Phplrt\Parser\Grammar\Concatenation([62, 61]),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        65 => new \Phplrt\Parser\Grammar\Repetition(63, 0, INF),
        66 => new \Phplrt\Parser\Grammar\Optional(64),
        67 => new \Phplrt\Parser\Grammar\Concatenation([70, 71]),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        69 => new \Phplrt\Parser\Grammar\Optional(68),
        70 => new \Phplrt\Parser\Grammar\Alternation([74, 77]),
        71 => new \Phplrt\Parser\Grammar\Optional(26),
        72 => new \Phplrt\Parser\Grammar\Concatenation([53, 79]),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        74 => new \Phplrt\Parser\Grammar\Concatenation([73, 72]),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        76 => new \Phplrt\Parser\Grammar\Optional(75),
        77 => new \Phplrt\Parser\Grammar\Concatenation([72, 76]),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        79 => new \Phplrt\Parser\Grammar\Optional(78),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        81 => new \Phplrt\Parser\Grammar\Concatenation([96, 99]),
        82 => new \Phplrt\Parser\Grammar\Concatenation([94, 95]),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        84 => new \Phplrt\Parser\Grammar\Concatenation([83, 82]),
        85 => new \Phplrt\Parser\Grammar\Optional(84),
        86 => new \Phplrt\Parser\Grammar\Concatenation([81, 85]),
        87 => new \Phplrt\Parser\Grammar\Optional(82),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        90 => new \Phplrt\Parser\Grammar\Alternation([86, 87]),
        91 => new \Phplrt\Parser\Grammar\Optional(88),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        93 => new \Phplrt\Parser\Grammar\Concatenation([89, 90, 91, 92]),
        94 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        95 => new \Phplrt\Parser\Grammar\Optional(50),
        96 => new \Phplrt\Parser\Grammar\Alternation([100, 101]),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        98 => new \Phplrt\Parser\Grammar\Concatenation([97, 96]),
        99 => new \Phplrt\Parser\Grammar\Repetition(98, 0, INF),
        100 => new \Phplrt\Parser\Grammar\Concatenation([102, 105, 106, 104]),
        101 => new \Phplrt\Parser\Grammar\Concatenation([104]),
        102 => new \Phplrt\Parser\Grammar\Alternation([3, 23, 21]),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        104 => new \Phplrt\Parser\Grammar\Concatenation([53]),
        105 => new \Phplrt\Parser\Grammar\Optional(103),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        107 => new \Phplrt\Parser\Grammar\Alternation([50, 93]),
        108 => new \Phplrt\Parser\Grammar\Optional(107),
        109 => new \Phplrt\Parser\Grammar\Concatenation([2, 108]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([123]),
        111 => new \Phplrt\Parser\Grammar\Optional(113),
        112 => new \Phplrt\Parser\Grammar\Concatenation([110, 111]),
        113 => new \Phplrt\Parser\Grammar\Concatenation([118, 119, 120, 53, 121, 53]),
        114 => new \Phplrt\Parser\Grammar\Concatenation([26, 113]),
        115 => new \Phplrt\Parser\Grammar\Alternation([112, 114]),
        116 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        118 => new \Phplrt\Parser\Grammar\Alternation([116, 117]),
        119 => new \Phplrt\Parser\Grammar\Alternation([26, 53]),
        120 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        122 => new \Phplrt\Parser\Grammar\Concatenation([115]),
        123 => new \Phplrt\Parser\Grammar\Concatenation([124, 127]),
        124 => new \Phplrt\Parser\Grammar\Concatenation([128, 131]),
        125 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        126 => new \Phplrt\Parser\Grammar\Concatenation([125, 123]),
        127 => new \Phplrt\Parser\Grammar\Optional(126),
        128 => new \Phplrt\Parser\Grammar\Concatenation([132]),
        129 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        130 => new \Phplrt\Parser\Grammar\Concatenation([129, 124]),
        131 => new \Phplrt\Parser\Grammar\Optional(130),
        132 => new \Phplrt\Parser\Grammar\Alternation([135, 133]),
        133 => new \Phplrt\Parser\Grammar\Concatenation([136, 140]),
        134 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        135 => new \Phplrt\Parser\Grammar\Concatenation([134, 133]),
        137 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        138 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        139 => new \Phplrt\Parser\Grammar\Concatenation([137, 138]),
        140 => new \Phplrt\Parser\Grammar\Repetition(139, 0, INF),
        141 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        142 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        143 => new \Phplrt\Parser\Grammar\Concatenation([141, 53, 142]),
        136 => new \Phplrt\Parser\Grammar\Alternation([143, 20, 60, 109])
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
        16 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Identifier($children->getValue());
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($this->literals === false) {
            throw FeatureNotAllowedException::fromFeature('literal values', $offset);
        }
        return $children;
        },
        26 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        21 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->stringPool[$token] ??= $children;
        },
        27 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        28 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        22 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        23 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        24 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        25 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        18 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        50 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        42 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \array_pop($children),
            $children[0] ?? null,
        );
        },
        60 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        54 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ParametersListNode($children);
        },
        61 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        70 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        72 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ParameterNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        93 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        81 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        100 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        101 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ImplicitFieldNode($children[0]);
        },
        109 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        115 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        123 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        124 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        132 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        133 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];