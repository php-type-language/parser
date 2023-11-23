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
            'T_THIS' => '\\$this(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_EQ' => '(?i)is(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NEQ' => '(?i)is\\h+not(?![a-zA-Z0-9\\-_\\x80-\\xff])',
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
        16 => new \Phplrt\Parser\Grammar\Concatenation([2, 36]),
        17 => new \Phplrt\Parser\Grammar\Concatenation([2, 40, 41]),
        18 => new \Phplrt\Parser\Grammar\Alternation([15, 16, 17]),
        19 => new \Phplrt\Parser\Grammar\Alternation([27, 28]),
        20 => new \Phplrt\Parser\Grammar\Alternation([29, 30, 31]),
        21 => new \Phplrt\Parser\Grammar\Alternation([32, 33, 34, 35]),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        26 => new \Phplrt\Parser\Grammar\Alternation([24, 25]),
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
        42 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        44 => new \Phplrt\Parser\Grammar\Concatenation([43, 42]),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        47 => new \Phplrt\Parser\Grammar\Repetition(44, 0, INF),
        48 => new \Phplrt\Parser\Grammar\Optional(45),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        50 => new \Phplrt\Parser\Grammar\Concatenation([46, 42, 47, 48, 49]),
        51 => new \Phplrt\Parser\Grammar\Concatenation([58, 62, 63]),
        52 => new \Phplrt\Parser\Grammar\Concatenation([77, 'Type']),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        54 => new \Phplrt\Parser\Grammar\Optional(51),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        56 => new \Phplrt\Parser\Grammar\Optional(52),
        57 => new \Phplrt\Parser\Grammar\Concatenation([2, 53, 54, 55, 56]),
        58 => new \Phplrt\Parser\Grammar\Concatenation([64, 66]),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        60 => new \Phplrt\Parser\Grammar\Concatenation([59, 58]),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        62 => new \Phplrt\Parser\Grammar\Repetition(60, 0, INF),
        63 => new \Phplrt\Parser\Grammar\Optional(61),
        64 => new \Phplrt\Parser\Grammar\Concatenation([67, 68]),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        66 => new \Phplrt\Parser\Grammar\Optional(65),
        67 => new \Phplrt\Parser\Grammar\Alternation([71, 74]),
        68 => new \Phplrt\Parser\Grammar\Optional(26),
        69 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 76]),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        71 => new \Phplrt\Parser\Grammar\Concatenation([70, 69]),
        72 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        73 => new \Phplrt\Parser\Grammar\Optional(72),
        74 => new \Phplrt\Parser\Grammar\Concatenation([69, 73]),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        76 => new \Phplrt\Parser\Grammar\Optional(75),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        78 => new \Phplrt\Parser\Grammar\Concatenation([93, 96]),
        79 => new \Phplrt\Parser\Grammar\Concatenation([91, 92]),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        81 => new \Phplrt\Parser\Grammar\Concatenation([80, 79]),
        82 => new \Phplrt\Parser\Grammar\Optional(81),
        83 => new \Phplrt\Parser\Grammar\Concatenation([78, 82]),
        84 => new \Phplrt\Parser\Grammar\Optional(79),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        87 => new \Phplrt\Parser\Grammar\Alternation([83, 84]),
        88 => new \Phplrt\Parser\Grammar\Optional(85),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        90 => new \Phplrt\Parser\Grammar\Concatenation([86, 87, 88, 89]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        92 => new \Phplrt\Parser\Grammar\Optional(50),
        93 => new \Phplrt\Parser\Grammar\Alternation([97, 98]),
        94 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        95 => new \Phplrt\Parser\Grammar\Concatenation([94, 93]),
        96 => new \Phplrt\Parser\Grammar\Repetition(95, 0, INF),
        97 => new \Phplrt\Parser\Grammar\Concatenation([99, 102, 103, 101]),
        98 => new \Phplrt\Parser\Grammar\Concatenation([101]),
        99 => new \Phplrt\Parser\Grammar\Alternation([3, 21, 19]),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        101 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        102 => new \Phplrt\Parser\Grammar\Optional(100),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        104 => new \Phplrt\Parser\Grammar\Alternation([50, 90]),
        105 => new \Phplrt\Parser\Grammar\Optional(104),
        106 => new \Phplrt\Parser\Grammar\Concatenation([2, 105]),
        107 => new \Phplrt\Parser\Grammar\Concatenation([120]),
        108 => new \Phplrt\Parser\Grammar\Optional(110),
        109 => new \Phplrt\Parser\Grammar\Concatenation([107, 108]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([115, 116, 117, 'Type', 118, 'Type']),
        111 => new \Phplrt\Parser\Grammar\Concatenation([26, 110]),
        112 => new \Phplrt\Parser\Grammar\Alternation([109, 111]),
        113 => new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        115 => new \Phplrt\Parser\Grammar\Alternation([113, 114]),
        116 => new \Phplrt\Parser\Grammar\Alternation(['Type', 26]),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        119 => new \Phplrt\Parser\Grammar\Concatenation([112]),
        120 => new \Phplrt\Parser\Grammar\Concatenation([121, 124]),
        121 => new \Phplrt\Parser\Grammar\Concatenation([125, 128]),
        122 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        123 => new \Phplrt\Parser\Grammar\Concatenation([122, 120]),
        124 => new \Phplrt\Parser\Grammar\Optional(123),
        125 => new \Phplrt\Parser\Grammar\Concatenation([129]),
        126 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        127 => new \Phplrt\Parser\Grammar\Concatenation([126, 121]),
        128 => new \Phplrt\Parser\Grammar\Optional(127),
        129 => new \Phplrt\Parser\Grammar\Alternation([132, 130]),
        130 => new \Phplrt\Parser\Grammar\Concatenation([133, 137]),
        131 => new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        132 => new \Phplrt\Parser\Grammar\Concatenation([131, 130]),
        134 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        135 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        136 => new \Phplrt\Parser\Grammar\Concatenation([134, 135]),
        137 => new \Phplrt\Parser\Grammar\Repetition(136, 0, INF),
        138 => new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        139 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        140 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        141 => new \Phplrt\Parser\Grammar\Concatenation([139, 'Type', 140]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([119]),
        133 => new \Phplrt\Parser\Grammar\Alternation([141, 18, 57, 106, 138])
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
        26 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        50 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        42 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        57 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        51 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ParametersListNode($children);
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        64 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        69 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ParameterNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        90 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        78 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        97 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        98 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        106 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        112 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        120 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        121 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        129 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        130 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        },
        138 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ThisTypeNode();
        }
    ]
];