<?php

use Hyper\Type\DSL\Node;

return [
    'initial' => 0,
    'tokens' => [
        'default' => [
            'T_STRING_LITERAL' => '(L?)"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_FLOAT_LITERAL' => '\\-?(?:[1-9]\\d*|[0-9])\\.(?:[1-9]\\d*|[0-9])',
            'T_INT_LITERAL' => '\\-?[1-9]\\d*|[0-9]',
            'T_BOOL_LITERAL' => '\\b(?i)(?:true|false)\\b',
            'T_NULL_LITERAL' => '\\b(?i)(?:null)\\b',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*',
            'T_ANGLE_BRACKET_OPEN' => '<',
            'T_ANGLE_BRACKET_CLOSE' => '>',
            'T_BRACE_OPEN' => '\\{',
            'T_BRACE_CLOSE' => '\\}',
            'T_COMMA' => ',',
            'T_DOUBLE_COLON' => '::',
            'T_COLON' => ':',
            'T_NS_DELIMITER' => '\\\\',
            'T_NULLABLE' => '\\?',
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
        0 => new \Phplrt\Parser\Grammar\Concatenation([9, 11]),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        4 => new \Phplrt\Parser\Grammar\Concatenation([2, 3]),
        5 => new \Phplrt\Parser\Grammar\Optional(1),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        7 => new \Phplrt\Parser\Grammar\Repetition(4, 0, INF),
        8 => new \Phplrt\Parser\Grammar\Concatenation([5, 6, 7]),
        9 => new \Phplrt\Parser\Grammar\Concatenation([8, 15]),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        11 => new \Phplrt\Parser\Grammar\Optional(10),
        12 => new \Phplrt\Parser\Grammar\Concatenation([27, 22, 28, 29]),
        13 => new \Phplrt\Parser\Grammar\Concatenation([19, 16, 20, 21]),
        14 => new \Phplrt\Parser\Grammar\Alternation([12, 13]),
        15 => new \Phplrt\Parser\Grammar\Optional(14),
        16 => new \Phplrt\Parser\Grammar\Concatenation([23, 24, 22]),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        18 => new \Phplrt\Parser\Grammar\Concatenation([17, 16]),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        20 => new \Phplrt\Parser\Grammar\Repetition(18, 0, INF),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        22 => new \Phplrt\Parser\Grammar\Alternation([30, 0]),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        26 => new \Phplrt\Parser\Grammar\Concatenation([25, 22]),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        28 => new \Phplrt\Parser\Grammar\Repetition(26, 0, INF),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        30 => new \Phplrt\Parser\Grammar\Alternation([31, 32, 33, 34, 35, 36]),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_STRING_LITERAL', true),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        36 => new \Phplrt\Parser\Grammar\Concatenation([8, 37, 38])
    ],
    'reducers' => [
        8 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\Name::parse($children);
        },
        0 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            return Node\Stmt\NullableTypeStmt::fromTypeStmt($children[0]);
        }
    
        return $children[0];
        },
        9 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            return new Node\Stmt\TypeStmt(
            offset: $offset,
            name: $children[0],
            args: $children[1] ?? [],
        );
        },
        13 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \ArrayObject($children);
        },
        16 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            return new Node\NamedArgument($offset, $children[0]->getValue(), $children[1]);
        },
        12 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \ArrayObject($children);
        },
        22 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            return new Node\Argument($offset, $children);
        },
        31 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteral::parse($token);
        },
        32 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteral::parse($token);
        },
        33 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteral::parse($token);
        },
        34 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteral::parse($token);
        },
        35 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\NullLiteral::parse($token);
        },
        36 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\Literal\ClassConstLiteral::parse($children[0], $children[1]);
        }
    ]
];