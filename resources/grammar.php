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
            'T_NAMESPACE' => 'namespace(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_TYPE' => 'type(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_INTERFACE' => 'interface(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_CLASS' => 'class(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_TRAIT' => 'trait(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_FUNCTION' => 'function(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_VISIBILITY_PUBLIC' => 'public(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_VISIBILITY_PROTECTED' => 'protected(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_VISIBILITY_PRIVATE' => 'private(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_IMPLEMENTS' => 'implements(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_EXTENDS' => 'extends(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_OUT' => 'out(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_IN' => 'in(?![a-zA-Z0-9\\-_\\x80-\\xff])',
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
        3 => new \Phplrt\Parser\Grammar\Alternation([11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26]),
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
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_NAMESPACE', true),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', true),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_INTERFACE', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_CLASS', true),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_TRAIT', true),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_FUNCTION', true),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PUBLIC', true),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PROTECTED', true),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PRIVATE', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_IMPLEMENTS', true),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_EXTENDS', true),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_OUT', true),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_IN', true),
        27 => new \Phplrt\Parser\Grammar\Alternation([36, 37]),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_INT_LITERAL', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        32 => new \Phplrt\Parser\Grammar\Concatenation([2, 38]),
        33 => new \Phplrt\Parser\Grammar\Concatenation([2, 42, 43]),
        34 => new \Phplrt\Parser\Grammar\Alternation([27, 28, 29, 30, 31, 32, 33]),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        40 => new \Phplrt\Parser\Grammar\Concatenation([3, 39]),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        43 => new \Phplrt\Parser\Grammar\Alternation([40, 3, 41]),
        44 => new \Phplrt\Parser\Grammar\Concatenation([51, 3, 53]),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        46 => new \Phplrt\Parser\Grammar\Concatenation([45, 44]),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        48 => new \Phplrt\Parser\Grammar\Repetition(46, 0, INF),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        50 => new \Phplrt\Parser\Grammar\Concatenation([47, 44, 48, 49]),
        51 => new \Phplrt\Parser\Grammar\Optional(57),
        52 => new \Phplrt\Parser\Grammar\Concatenation([54, 'Type']),
        53 => new \Phplrt\Parser\Grammar\Optional(52),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_IN', true),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_OUT', true),
        57 => new \Phplrt\Parser\Grammar\Alternation([55, 56]),
        58 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        60 => new \Phplrt\Parser\Grammar\Concatenation([59, 58]),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        63 => new \Phplrt\Parser\Grammar\Repetition(60, 0, INF),
        64 => new \Phplrt\Parser\Grammar\Optional(61),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        66 => new \Phplrt\Parser\Grammar\Concatenation([62, 58, 63, 64, 65]),
        67 => new \Phplrt\Parser\Grammar\Concatenation([74, 78, 79]),
        68 => new \Phplrt\Parser\Grammar\Concatenation([93, 'Type']),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        70 => new \Phplrt\Parser\Grammar\Optional(67),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        72 => new \Phplrt\Parser\Grammar\Optional(68),
        73 => new \Phplrt\Parser\Grammar\Concatenation([2, 69, 70, 71, 72]),
        74 => new \Phplrt\Parser\Grammar\Concatenation([80, 82]),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        76 => new \Phplrt\Parser\Grammar\Concatenation([75, 74]),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        78 => new \Phplrt\Parser\Grammar\Repetition(76, 0, INF),
        79 => new \Phplrt\Parser\Grammar\Optional(77),
        80 => new \Phplrt\Parser\Grammar\Concatenation([83, 84]),
        81 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        82 => new \Phplrt\Parser\Grammar\Optional(81),
        83 => new \Phplrt\Parser\Grammar\Alternation([87, 90]),
        84 => new \Phplrt\Parser\Grammar\Optional(35),
        85 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 92]),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        87 => new \Phplrt\Parser\Grammar\Concatenation([86, 85]),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        89 => new \Phplrt\Parser\Grammar\Optional(88),
        90 => new \Phplrt\Parser\Grammar\Concatenation([85, 89]),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        92 => new \Phplrt\Parser\Grammar\Optional(91),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        94 => new \Phplrt\Parser\Grammar\Concatenation([97, 3, 98, 99, 100, 101, 102]),
        95 => new \Phplrt\Parser\Grammar\Concatenation([94]),
        96 => new \Phplrt\Parser\Grammar\Concatenation([103, 'Type']),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_FUNCTION', false),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        99 => new \Phplrt\Parser\Grammar\Optional(67),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        101 => new \Phplrt\Parser\Grammar\Optional(96),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_SEMICOLON', false),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        104 => new \Phplrt\Parser\Grammar\Optional(109),
        105 => new \Phplrt\Parser\Grammar\Concatenation([104, 94]),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PUBLIC', true),
        107 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PROTECTED', true),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_VISIBILITY_PRIVATE', true),
        109 => new \Phplrt\Parser\Grammar\Alternation([106, 107, 108]),
        110 => new \Phplrt\Parser\Grammar\Concatenation([125, 128]),
        111 => new \Phplrt\Parser\Grammar\Concatenation([123, 124]),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        113 => new \Phplrt\Parser\Grammar\Concatenation([112, 111]),
        114 => new \Phplrt\Parser\Grammar\Optional(113),
        115 => new \Phplrt\Parser\Grammar\Concatenation([110, 114]),
        116 => new \Phplrt\Parser\Grammar\Optional(111),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        119 => new \Phplrt\Parser\Grammar\Alternation([115, 116]),
        120 => new \Phplrt\Parser\Grammar\Optional(117),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        122 => new \Phplrt\Parser\Grammar\Concatenation([118, 119, 120, 121]),
        123 => new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        124 => new \Phplrt\Parser\Grammar\Optional(66),
        125 => new \Phplrt\Parser\Grammar\Alternation([129, 130]),
        126 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        127 => new \Phplrt\Parser\Grammar\Concatenation([126, 125]),
        128 => new \Phplrt\Parser\Grammar\Repetition(127, 0, INF),
        129 => new \Phplrt\Parser\Grammar\Concatenation([131, 134, 135, 133]),
        130 => new \Phplrt\Parser\Grammar\Concatenation([133]),
        131 => new \Phplrt\Parser\Grammar\Alternation([3, 29, 27]),
        132 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        133 => new \Phplrt\Parser\Grammar\Concatenation(['Type']),
        134 => new \Phplrt\Parser\Grammar\Optional(132),
        135 => new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        136 => new \Phplrt\Parser\Grammar\Alternation([66, 122]),
        137 => new \Phplrt\Parser\Grammar\Optional(136),
        138 => new \Phplrt\Parser\Grammar\Concatenation([2, 137]),
        139 => new \Phplrt\Parser\Grammar\Lexeme('T_EXTENDS', false),
        140 => new \Phplrt\Parser\Grammar\Concatenation([139, 138]),
        141 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        142 => new \Phplrt\Parser\Grammar\Concatenation([141, 138]),
        143 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        144 => new \Phplrt\Parser\Grammar\Lexeme('T_EXTENDS', false),
        145 => new \Phplrt\Parser\Grammar\Repetition(142, 0, INF),
        146 => new \Phplrt\Parser\Grammar\Optional(143),
        147 => new \Phplrt\Parser\Grammar\Concatenation([144, 138, 145, 146]),
        148 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        149 => new \Phplrt\Parser\Grammar\Concatenation([148, 138]),
        150 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        151 => new \Phplrt\Parser\Grammar\Lexeme('T_IMPLEMENTS', false),
        152 => new \Phplrt\Parser\Grammar\Repetition(149, 0, INF),
        153 => new \Phplrt\Parser\Grammar\Optional(150),
        154 => new \Phplrt\Parser\Grammar\Concatenation([151, 138, 152, 153]),
        155 => new \Phplrt\Parser\Grammar\Concatenation([160, 2, 161, 162]),
        156 => new \Phplrt\Parser\Grammar\Concatenation([163, 2, 164, 165, 166]),
        157 => new \Phplrt\Parser\Grammar\Repetition(156, 1, INF),
        158 => new \Phplrt\Parser\Grammar\Alternation([155, 157]),
        159 => new \Phplrt\Parser\Grammar\Repetition(167, 1, INF),
        160 => new \Phplrt\Parser\Grammar\Lexeme('T_NAMESPACE', false),
        161 => new \Phplrt\Parser\Grammar\Lexeme('T_SEMICOLON', false),
        162 => new \Phplrt\Parser\Grammar\Optional(159),
        163 => new \Phplrt\Parser\Grammar\Lexeme('T_NAMESPACE', false),
        164 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        165 => new \Phplrt\Parser\Grammar\Optional(159),
        166 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        167 => new \Phplrt\Parser\Grammar\Alternation([194, 184, 174, 178]),
        168 => new \Phplrt\Parser\Grammar\Lexeme('T_SEMICOLON', false),
        169 => new \Phplrt\Parser\Grammar\Concatenation([185, 186, 187]),
        170 => new \Phplrt\Parser\Grammar\Lexeme('T_INTERFACE', false),
        171 => new \Phplrt\Parser\Grammar\Optional(50),
        172 => new \Phplrt\Parser\Grammar\Optional(147),
        173 => new \Phplrt\Parser\Grammar\Alternation([168, 169]),
        174 => new \Phplrt\Parser\Grammar\Concatenation([170, 3, 171, 172, 173]),
        175 => new \Phplrt\Parser\Grammar\Lexeme('T_TRAIT', false),
        176 => new \Phplrt\Parser\Grammar\Optional(50),
        177 => new \Phplrt\Parser\Grammar\Alternation([168, 169]),
        178 => new \Phplrt\Parser\Grammar\Concatenation([175, 3, 176, 177]),
        179 => new \Phplrt\Parser\Grammar\Lexeme('T_CLASS', false),
        180 => new \Phplrt\Parser\Grammar\Optional(50),
        181 => new \Phplrt\Parser\Grammar\Optional(140),
        182 => new \Phplrt\Parser\Grammar\Optional(154),
        183 => new \Phplrt\Parser\Grammar\Alternation([168, 169]),
        184 => new \Phplrt\Parser\Grammar\Concatenation([179, 3, 180, 181, 182, 183]),
        185 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        186 => new \Phplrt\Parser\Grammar\Repetition(105, 1, INF),
        187 => new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        188 => new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', false),
        189 => new \Phplrt\Parser\Grammar\Concatenation([188, 'Type']),
        190 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        191 => new \Phplrt\Parser\Grammar\Optional(50),
        192 => new \Phplrt\Parser\Grammar\Optional(189),
        193 => new \Phplrt\Parser\Grammar\Lexeme('T_SEMICOLON', false),
        194 => new \Phplrt\Parser\Grammar\Concatenation([190, 3, 191, 192, 193]),
        195 => new \Phplrt\Parser\Grammar\Repetition(167, 0, INF),
        196 => new \Phplrt\Parser\Grammar\Concatenation([197]),
        197 => new \Phplrt\Parser\Grammar\Concatenation([198, 201]),
        198 => new \Phplrt\Parser\Grammar\Concatenation([202, 205]),
        199 => new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        200 => new \Phplrt\Parser\Grammar\Concatenation([199, 197]),
        201 => new \Phplrt\Parser\Grammar\Optional(200),
        202 => new \Phplrt\Parser\Grammar\Concatenation([206]),
        203 => new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        204 => new \Phplrt\Parser\Grammar\Concatenation([203, 198]),
        205 => new \Phplrt\Parser\Grammar\Optional(204),
        206 => new \Phplrt\Parser\Grammar\Alternation([209, 210]),
        207 => new \Phplrt\Parser\Grammar\Concatenation([213, 217]),
        208 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        209 => new \Phplrt\Parser\Grammar\Concatenation([208, 207]),
        210 => new \Phplrt\Parser\Grammar\Concatenation([207, 212]),
        211 => new \Phplrt\Parser\Grammar\Lexeme('T_NULLABLE', true),
        212 => new \Phplrt\Parser\Grammar\Optional(211),
        214 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        215 => new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        216 => new \Phplrt\Parser\Grammar\Concatenation([214, 215]),
        217 => new \Phplrt\Parser\Grammar\Repetition(216, 0, INF),
        218 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        219 => new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        220 => new \Phplrt\Parser\Grammar\Concatenation([218, 'Type', 219]),
        'Document' => new \Phplrt\Parser\Grammar\Alternation([158, 195]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([196]),
        213 => new \Phplrt\Parser\Grammar\Alternation([220, 34, 73, 138])
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
        35 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        27 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->stringPool[$token] ??= $children;
        },
        36 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        37 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        28 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        29 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        30 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        31 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        32 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        33 => function (\Phplrt\Parser\Context $ctx, $children) {
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
            return new Node\Stmt\Template\ParametersListNode($children);
        },
        44 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ParameterNode(
            $children[1],
            $children[0],
            $children[2] ?? null,
        );
        },
        51 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($children === []) {
            return Node\Stmt\Template\ParameterVariance::ANY;
        }
    
        return match ($children->getValue()) {
            'in' => Node\Stmt\Template\ParameterVariance::IN,
            'out' => Node\Stmt\Template\ParameterVariance::OUT,
            default => throw new SemanticException(
                \sprintf('Unprocessable type variance "%s"', $children->getValue()),
                $offset,
            ),
        };
        },
        66 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentsListNode($children);
        },
        58 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Template\ArgumentNode(
            \is_array($children) ? $children[0] : $children,
        );
        },
        73 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\ArgumentsListNode($children);
        },
        74 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        80 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
            return $children[0];
        }
    
        $children[0]->name = $children[1];
        return $children[0];
        },
        83 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        85 => function (\Phplrt\Parser\Context $ctx, $children) {
            $argument = new Node\Stmt\Callable\ArgumentNode($children[0]);
    
        if (\count($children) !== 1) {
            $argument->output = true;
        }
    
        return $argument;
        },
        95 => function (\Phplrt\Parser\Context $ctx, $children) {
            $arguments = $type = null;
    
        if (\end($children) instanceof Node\Stmt\NamedTypeNode) {
            $type = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Callable\ArgumentsListNode) {
            $arguments = \array_pop($children);
        }
    
        return new Node\Stmt\Callable\FunctionDefinitionNode(
            $children[0],
            $arguments,
            $type,
        );
        },
        105 => function (\Phplrt\Parser\Context $ctx, $children) {
            $arguments = $type = null;
    
        if (\end($children) instanceof Node\Stmt\NamedTypeNode) {
            $type = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Callable\ArgumentsListNode) {
            $arguments = \array_pop($children);
        }
    
        return new Node\Stmt\Callable\MethodDefinitionNode(
            $children[0],
            $children[1],
            $arguments,
            $type,
        );
        },
        104 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            if ($children === []) {
            return Node\Stmt\Callable\Visibility::PUBLIC;
        }
    
        return match ($children->getName()) {
            'T_VISIBILITY_PUBLIC' => Node\Stmt\Callable\Visibility::PUBLIC,
            'T_VISIBILITY_PROTECTED' => Node\Stmt\Callable\Visibility::PROTECTED,
            'T_VISIBILITY_PRIVATE' => Node\Stmt\Callable\Visibility::PRIVATE,
            default => throw new SemanticException(
                \sprintf('Unprocessable method visibility "%s"', $children->getValue()),
                $offset,
            ),
        };
        },
        122 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        110 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        129 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        130 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\FieldNode($children[0]);
        },
        138 => function (\Phplrt\Parser\Context $ctx, $children) {
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
        140 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ClassLike\ClassExtendsNode($children[0]);
        },
        147 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ClassLike\InterfaceImplementsNode($children);
        },
        154 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ClassLike\InterfaceImplementsNode($children);
        },
        155 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\NamespaceNode(
            $children[0],
            Node\Stmt\NamespaceType::SEMICOLON,
            $children[1] ?? null,
        );
        },
        156 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\NamespaceNode(
            $children[0],
            Node\Stmt\NamespaceType::BRACED,
            $children[1] ?? null,
        );
        },
        159 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\DefinitionsListNode($children);
        },
        174 => function (\Phplrt\Parser\Context $ctx, $children) {
            $implements = $parameters = $methods = null;
    
        if (\end($children) instanceof Node\Stmt\Callable\MethodsListNode) {
            $methods = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\ClassLike\InterfaceImplementsNode) {
            $implements = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\ClassLike\InterfaceDefinitionNode(
            $children[0],
            $parameters,
            $methods,
            $implements,
        );
        },
        178 => function (\Phplrt\Parser\Context $ctx, $children) {
            $parameters = $methods = null;
    
        if (\end($children) instanceof Node\Stmt\Callable\MethodsListNode) {
            $methods = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\ClassLike\TraitDefinitionNode(
            $children[0],
            $parameters,
            $methods,
        );
        },
        184 => function (\Phplrt\Parser\Context $ctx, $children) {
            $extends = $implements = $parameters = $methods = null;
    
        if (\end($children) instanceof Node\Stmt\Callable\MethodsListNode) {
            $methods = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\ClassLike\InterfaceImplementsNode) {
            $implements = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\ClassLike\ClassExtendsNode) {
            $extends = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\ClassLike\ClassDefinitionNode(
            $children[0],
            $parameters,
            $methods,
            $extends,
            $implements,
        );
        },
        169 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\MethodsListNode($children);
        },
        194 => function (\Phplrt\Parser\Context $ctx, $children) {
            $type = $parameters = null;
    
        if (\end($children) instanceof Node\Stmt\TypeStatement) {
            $type = \array_pop($children);
        }
    
        if (\end($children) instanceof Node\Stmt\Template\ParametersListNode) {
            $parameters = \array_pop($children);
        }
    
        return new Node\Stmt\TypeDefinitionNode($children[0], $parameters, $type);
        },
        197 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        198 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 2) {
            return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
        }
    
        return $children;
        },
        206 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
            return new Node\Stmt\NullableTypeNode($children[1]);
        }
    
        return $children;
        },
        210 => function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) > 1) {
            $result = new Node\Stmt\NullableTypeNode($children[0]);
            $result->offset = $children[1]->getOffset();
    
            return $result;
        }
    
        return $children[0];
        },
        207 => function (\Phplrt\Parser\Context $ctx, $children) {
            $statement = \array_shift($children);
    
        for ($i = 0, $length = \count($children); $i < $length; ++$i) {
            $statement = new Node\Stmt\TypesListNode($statement);
            $statement->offset = $children[$i]->getOffset();
        }
    
        return $statement;
        }
    ]
];