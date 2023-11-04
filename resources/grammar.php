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
            'T_THIS' => '\\$this',
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
        14 => new \Phplrt\Parser\Grammar\Alternation([25, 26]),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        19 => new \Phplrt\Parser\Grammar\Concatenation([2, 27]),
        20 => new \Phplrt\Parser\Grammar\Concatenation([2, 31, 32]),
        21 => new \Phplrt\Parser\Grammar\Alternation([14, 15, 16, 17, 18, 19, 20]),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        24 => new \Phplrt\Parser\Grammar\Alternation([22, 23]),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        29 => new \Phplrt\Parser\Grammar\Concatenation([3, 28]),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        32 => new \Phplrt\Parser\Grammar\Alternation([29, 3, 30]),
        33 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        35 => new \Phplrt\Parser\Grammar\Concatenation([34, 33]),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        38 => new \Phplrt\Parser\Grammar\Repetition(35, 0, INF),
        39 => new \Phplrt\Parser\Grammar\Optional(36),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        41 => new \Phplrt\Parser\Grammar\Concatenation([37, 33, 38, 39, 40]),
        42 => new \Phplrt\Parser\Grammar\Concatenation([49, 53, 54]),
        43 => new \Phplrt\Parser\Grammar\Concatenation([68, 'Type']),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        45 => new \Phplrt\Parser\Grammar\Optional(42),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        47 => new \Phplrt\Parser\Grammar\Optional(43),
        48 => new \Phplrt\Parser\Grammar\Concatenation([2, 44, 45, 46, 47]),
        49 => new \Phplrt\Parser\Grammar\Concatenation([55, 57]),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        51 => new \Phplrt\Parser\Grammar\Concatenation([50, 49]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        53 => new \Phplrt\Parser\Grammar\Repetition(51, 0, INF),
        54 => new \Phplrt\Parser\Grammar\Optional(52),
        55 => new \Phplrt\Parser\Grammar\Concatenation([58, 59]),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        57 => new \Phplrt\Parser\Grammar\Optional(56),
        58 => new \Phplrt\Parser\Grammar\Alternation([62, 65]),
        59 => new \Phplrt\Parser\Grammar\Optional(24),
        60 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 67]),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        62 => new \Phplrt\Parser\Grammar\Concatenation([61, 60]),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        64 => new \Phplrt\Parser\Grammar\Optional(63),
        65 => new \Phplrt\Parser\Grammar\Concatenation([60, 64]),
        66 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        67 => new \Phplrt\Parser\Grammar\Optional(66),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        69 => new \Phplrt\Parser\Grammar\Concatenation([84, 87]),
        70 => new \Phplrt\Parser\Grammar\Concatenation([82, 83]),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        72 => new \Phplrt\Parser\Grammar\Concatenation([71, 70]),
        73 => new \Phplrt\Parser\Grammar\Optional(72),
        74 => new \Phplrt\Parser\Grammar\Concatenation([69, 73]),
        75 => new \Phplrt\Parser\Grammar\Optional(70),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        78 => new \Phplrt\Parser\Grammar\Alternation([74, 75]),
        79 => new \Phplrt\Parser\Grammar\Optional(76),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        81 => new \Phplrt\Parser\Grammar\Concatenation([77, 78, 79, 80]),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        83 => new \Phplrt\Parser\Grammar\Optional(41),
        84 => new \Phplrt\Parser\Grammar\Alternation([88, 89]),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        86 => new \Phplrt\Parser\Grammar\Concatenation([85, 84]),
        87 => new \Phplrt\Parser\Grammar\Repetition(86, 0, INF),
        88 => new \Phplrt\Parser\Grammar\Concatenation([90, 93, 94, 92]),
        89 => new \Phplrt\Parser\Grammar\Concatenation([92]),
        90 => new \Phplrt\Parser\Grammar\Alternation([3, 16, 14]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        92 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        93 => new \Phplrt\Parser\Grammar\Optional(91),
        94 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        95 => new \Phplrt\Parser\Grammar\Alternation([41, 81]),
        96 => new \Phplrt\Parser\Grammar\Optional(95),
        97 => new \Phplrt\Parser\Grammar\Concatenation([2, 96]),
        98 => new \Phplrt\Parser\Grammar\Concatenation([99]),
        99 => new \Phplrt\Parser\Grammar\Concatenation([100, 103]),
        100 => new \Phplrt\Parser\Grammar\Concatenation([104, 107]),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        102 => new \Phplrt\Parser\Grammar\Concatenation([101, 99]),
        103 => new \Phplrt\Parser\Grammar\Optional(102),
        104 => new \Phplrt\Parser\Grammar\Concatenation([108]),
        105 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        106 => new \Phplrt\Parser\Grammar\Concatenation([105, 100]),
        107 => new \Phplrt\Parser\Grammar\Optional(106),
        108 => new \Phplrt\Parser\Grammar\Alternation([111, 109]),
        109 => new \Phplrt\Parser\Grammar\Concatenation([112, 116]),
        110 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        111 => new \Phplrt\Parser\Grammar\Concatenation([110, 109]),
        113 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        115 => new \Phplrt\Parser\Grammar\Concatenation([113, 114]),
        116 => new \Phplrt\Parser\Grammar\Repetition(115, 0, INF),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        119 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        120 => new \Phplrt\Parser\Grammar\Concatenation([118, 'Type', 119]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([98]),
        112 => new \Phplrt\Parser\Grammar\Alternation([120, 21, 48, 97, 117])
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
        24 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        14 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        41 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        33 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        48 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        42 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        49 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        55 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        60 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        81 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        69 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        88 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        89 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        97 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        99 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        100 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        108 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        109 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        },
        117 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ThisTypeNode();
        }
    ]
];