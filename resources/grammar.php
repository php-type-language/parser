<?php

use Phplrt\Parser\Context;
use Phplrt\Parser\Grammar\Alternation;
use Phplrt\Parser\Grammar\Concatenation;
use Phplrt\Parser\Grammar\Lexeme;
use Phplrt\Parser\Grammar\Optional;
use Phplrt\Parser\Grammar\Repetition;
use TypeLang\Parser\Node;

return [
    'initial'     => 26,
    'tokens'      => [
        'default' => [
            'T_DQ_STRING_LITERAL'   => '"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_SQ_STRING_LITERAL'   => '\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'',
            'T_FLOAT_LITERAL'       => '(?i)(?:-?[0-9]++\\.[0-9]*+(?:e-?[0-9]++)?)|(?:-?[0-9]*+\\.[0-9]++(?:e-?[0-9]++)?)|(?:-?[0-9]++e-?[0-9]++)',
            'T_INT_LITERAL'         => '\\-?(?i)(?:(?:0b[0-1_]++)|(?:0o[0-7_]++)|(?:0x[0-9a-f_]++)|(?:[0-9][0-9_]*+))',
            'T_BOOL_LITERAL'        => '\\b(?i)(?:true|false)\\b',
            'T_NULL_LITERAL'        => '\\b(?i)(?:null)\\b',
            'T_NAME'                => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_ANGLE_BRACKET_OPEN'  => '<',
            'T_ANGLE_BRACKET_CLOSE' => '>',
            'T_PARENTHESIS_OPEN'    => '\\(',
            'T_PARENTHESIS_CLOSE'   => '\\)',
            'T_BRACE_OPEN'          => '\\{',
            'T_BRACE_CLOSE'         => '\\}',
            'T_COMMA'               => ',',
            'T_ELLIPSIS'            => '\\.\\.\\.',
            'T_DOUBLE_COLON'        => '::',
            'T_COLON'               => ':',
            'T_EQ'                  => '=',
            'T_NS_DELIMITER'        => '\\\\',
            'T_NULLABLE'            => '\\?',
            'T_NOT'                 => '\\!',
            'T_OR'                  => '\\|',
            'T_AND'                 => '&',
            'T_ASTERISK'            => '\\*',
            'T_WHITESPACE'          => '\\s+',
            'T_BLOCK_COMMENT'       => '\\h*/\\*.*?\\*/\\h*',
        ],
    ],
    'skip'        => [
        'T_WHITESPACE',
        'T_BLOCK_COMMENT',
    ],
    'transitions' => [

    ],
    'grammar'     => [
        0   => new Alternation([7, 8]),
        1   => new Lexeme('T_FLOAT_LITERAL', true),
        2   => new Lexeme('T_INT_LITERAL', true),
        3   => new Lexeme('T_BOOL_LITERAL', true),
        4   => new Lexeme('T_NULL_LITERAL', true),
        5   => new Concatenation([9, 15, 16]),
        6   => new Alternation([0, 1, 2, 3, 4, 5]),
        7   => new Lexeme('T_SQ_STRING_LITERAL', true),
        8   => new Lexeme('T_DQ_STRING_LITERAL', true),
        9   => new Alternation([50, 51]),
        10  => new Lexeme('T_NAME', true),
        11  => new Lexeme('T_ASTERISK', true),
        12  => new Concatenation([10, 11]),
        13  => new Lexeme('T_NAME', true),
        14  => new Lexeme('T_ASTERISK', true),
        15  => new Lexeme('T_DOUBLE_COLON', false),
        16  => new Alternation([12, 13, 14]),
        17  => new Concatenation([26]),
        18  => new Lexeme('T_COMMA', false),
        19  => new Concatenation([18, 17]),
        20  => new Lexeme('T_COMMA', false),
        21  => new Lexeme('T_ANGLE_BRACKET_OPEN', false),
        22  => new Repetition(19, 0, INF),
        23  => new Optional(20),
        24  => new Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        25  => new Concatenation([21, 17, 22, 23, 24]),
        26  => new Concatenation([88]),
        27  => new Alternation([41, 42, 43]),
        28  => new Lexeme('T_COMMA', false),
        29  => new Concatenation([28, 27]),
        30  => new Lexeme('T_COMMA', false),
        31  => new Optional(40),
        32  => new Lexeme('T_COMMA', false),
        33  => new Lexeme('T_BRACE_OPEN', false),
        34  => new Optional(27),
        35  => new Repetition(29, 0, INF),
        36  => new Optional(30),
        37  => new Optional(32),
        38  => new Lexeme('T_BRACE_CLOSE', false),
        39  => new Concatenation([33, 34, 35, 36, 31, 37, 38]),
        40  => new Lexeme('T_ELLIPSIS', true),
        41  => new Concatenation([44, 46, 47, 45]),
        42  => new Concatenation([44, 48, 45]),
        43  => new Concatenation([45]),
        44  => new Alternation([49, 2, 3, 4, 0]),
        45  => new Concatenation([26]),
        46  => new Lexeme('T_NULLABLE', false),
        47  => new Lexeme('T_COLON', false),
        48  => new Lexeme('T_COLON', false),
        49  => new Lexeme('T_NAME', true),
        50  => new Concatenation([55, 56, 57]),
        51  => new Concatenation([61, 62]),
        52  => new Lexeme('T_NS_DELIMITER', false),
        53  => new Lexeme('T_NAME', true),
        54  => new Concatenation([52, 53]),
        55  => new Lexeme('T_NS_DELIMITER', false),
        56  => new Lexeme('T_NAME', true),
        57  => new Repetition(54, 0, INF),
        58  => new Lexeme('T_NS_DELIMITER', false),
        59  => new Lexeme('T_NAME', true),
        60  => new Concatenation([58, 59]),
        61  => new Lexeme('T_NAME', true),
        62  => new Repetition(60, 0, INF),
        63  => new Concatenation([70, 74, 75]),
        64  => new Concatenation([84, 26]),
        65  => new Lexeme('T_PARENTHESIS_OPEN', false),
        66  => new Optional(63),
        67  => new Lexeme('T_PARENTHESIS_CLOSE', false),
        68  => new Optional(64),
        69  => new Concatenation([9, 65, 66, 67, 68]),
        70  => new Concatenation([76]),
        71  => new Lexeme('T_COMMA', false),
        72  => new Concatenation([71, 70]),
        73  => new Lexeme('T_COMMA', false),
        74  => new Repetition(72, 0, INF),
        75  => new Optional(73),
        76  => new Alternation([78, 79]),
        77  => new Lexeme('T_ELLIPSIS', true),
        78  => new Concatenation([77, 26]),
        79  => new Concatenation([26, 83]),
        80  => new Lexeme('T_EQ', true),
        81  => new Lexeme('T_ELLIPSIS', true),
        82  => new Alternation([80, 81]),
        83  => new Optional(82),
        84  => new Lexeme('T_COLON', false),
        85  => new Alternation([25, 39]),
        86  => new Optional(85),
        87  => new Concatenation([9, 86]),
        88  => new Concatenation([89]),
        89  => new Concatenation([90, 93]),
        90  => new Concatenation([94, 97]),
        91  => new Lexeme('T_OR', false),
        92  => new Concatenation([91, 89]),
        93  => new Optional(92),
        94  => new Concatenation([98]),
        95  => new Lexeme('T_AND', false),
        96  => new Concatenation([95, 90]),
        97  => new Optional(96),
        98  => new Alternation([101, 104]),
        99  => new Alternation([107, 6, 69, 87]),
        100 => new Lexeme('T_NULLABLE', true),
        101 => new Concatenation([100, 99]),
        102 => new Lexeme('T_NULLABLE', true),
        103 => new Optional(102),
        104 => new Concatenation([99, 103]),
        105 => new Lexeme('T_PARENTHESIS_OPEN', false),
        106 => new Lexeme('T_PARENTHESIS_CLOSE', false),
        107 => new Concatenation([105, 26, 106]),
    ],
    'reducers'    => [
        0  => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Literal\StringLiteralStmt {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralStmt::parse($token, $token->getName() === 'T_DQ_STRING_LITERAL');
        },
        1  => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Literal\FloatLiteralStmt {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralStmt::parse($token);
        },
        2  => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Literal\IntLiteralStmt {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteralStmt::parse($token);
        },
        3  => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Literal\BoolLiteralStmt {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralStmt::parse($token);
        },
        4  => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Literal\NullLiteralStmt => new Node\Literal\NullLiteralStmt($children->getValue()),
        5  => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Stmt\ClassConstMaskStmt|\TypeLang\Parser\Node\Stmt\ClassConstStmt {
            if ((is_countable($children) ? \count($children) : 0) === 3) {
                return new Node\Stmt\ClassConstMaskStmt(
                    $children[0], $children[1]->getValue(),
                );
            }
            if ($children[1]->getName() === 'T_ASTERISK') {
                return new Node\Stmt\ClassConstMaskStmt($children[0]);
            }
            return new Node\Stmt\ClassConstStmt(
                $children[0], $children[1]->getValue()
            );
        },
        25 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Template\Parameters => new Node\Stmt\Template\Parameters($children),
        17 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Template\Parameter => new Node\Stmt\Template\Parameter(
            \is_array($children) ? $children[0] : $children,
        ),
        39 => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Stmt\Shape\Arguments {
            $isSealed = \array_pop($children);
            return new Node\Stmt\Shape\Arguments($children, $isSealed);
        },
        31 => static fn(Context $ctx, $children): bool => $children === [],
        41 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Shape\Argument => new Node\Stmt\Shape\Argument(
            $children[1], $children[0], true,
        ),
        42 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Shape\Argument => new Node\Stmt\Shape\Argument(
            $children[1], $children[0],
        ),
        43 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Shape\Argument => new Node\Stmt\Shape\Argument(
            $children[0],
        ),
        44 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Literal\StringLiteralStmt => match (true) {
            $children instanceof Node\Literal\StringLiteralStmt => $children,
            $children instanceof Node\Literal\IntLiteralStmt, $children instanceof Node\Literal\BoolLiteral, $children instanceof Node\Literal\NullLiteralStmt, => new Node\Literal\StringLiteralStmt(
                $children->raw
            ),
            default => new Node\Literal\StringLiteralStmt($children->getValue()),
        },
        50 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\FullQualifiedName => Node\FullQualifiedName::parse($children),
        51 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Name => Node\Name::parse($children),
        69 => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Stmt\CallableTypeStmt {
            $name = \array_shift($children);
            $arguments = isset($children[0]) && $children[0] instanceof Node\Stmt\Callable\Arguments ? \array_shift(
                $children
            ) : new Node\Stmt\Callable\Arguments();
            return new Node\Stmt\CallableTypeStmt(
                name: $name, arguments: $arguments, type: $children[0] ?? null,
            );
        },
        63 => static fn(Context $ctx, $children): \TypeLang\Parser\Node\Stmt\Callable\Arguments => new Node\Stmt\Callable\Arguments(
            list: $children,
        ),
        76 => static function (Context $ctx, $children) {
            if (\is_array($children)) {
                return new Node\Stmt\Callable\Argument(
                    type: $children[1], modifier: Node\Stmt\Callable\Modifier::VARIADIC,
                );
            }
            return $children;
        },
        79 => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Stmt\Callable\Argument {
            $modifier = null;
            if (isset($children[1])) {
                $modifier = $children[1]->getName(
                ) === 'T_EQ' ? Node\Stmt\Callable\Modifier::OPTIONAL : Node\Stmt\Callable\Modifier::VARIADIC;
            }
            return new Node\Stmt\Callable\Argument(
                type: $children[0], modifier: $modifier,
            );
        },
        87 => static function (Context $ctx, $children) : \TypeLang\Parser\Node\Stmt\NamedTypeStmt {
            $arguments = $parameters = null;
            $options = \end($children);
            if ($options instanceof Node\Stmt\Shape\Arguments) {
                $arguments = $options;
            } elseif ($options instanceof Node\Stmt\Template\Parameters) {
                $parameters = $options;
            }
            return new Node\Stmt\NamedTypeStmt(
                name: $children[0], parameters: $parameters, arguments: $arguments,
            );
        },
        89 => static function (Context $ctx, $children) {
            if ((is_countable($children) ? \count($children) : 0) === 2) {
                return new Node\Stmt\UnionTypeStmt($children[0], $children[1]);
            }
            return $children;
        },
        90 => static function (Context $ctx, $children) {
            if ((is_countable($children) ? \count($children) : 0) === 2) {
                return new Node\Stmt\IntersectionTypeStmt($children[0], $children[1]);
            }
            return $children;
        },
        98 => static function (Context $ctx, $children) {
            if ((is_countable($children) ? \count($children) : 0) > 1) {
                $statement = $children[0] instanceof Node\Stmt\Statement ? $children[0] : $children[1];

                return new Node\Stmt\NullableTypeStmt($statement);
            }
            return $children[0];
        }
    ]
];
