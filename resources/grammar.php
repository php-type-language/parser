<?php

declare(strict_types=1);

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\FeatureNotAllowedException;

/**
 * @var array{
 *     initial: array-key,
 *     tokens: array{
 *         default: array<non-empty-string, non-empty-string>,
 *         ...
 *     },
 *     skip: list<non-empty-string>,
 *     grammar: array<array-key, \Phplrt\Parser\Grammar\RuleInterface>,
 *     reducers: array<array-key, callable(\Phplrt\Parser\Context, mixed):mixed>,
 *     transitions?: array<array-key, mixed>
 * }
 */
return [
    'initial' => 59,
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
            'T_THIS' => '\\$this\\b',
            'T_VARIABLE' => '\\$[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_NAME_WITH_SPACE' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*\\s+?',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*',
            'T_LTE' => '<=',
            'T_GTE' => '>=',
            'T_ANGLE_BRACKET_OPEN' => '<',
            'T_ANGLE_BRACKET_CLOSE' => '>',
            'T_PARENTHESIS_OPEN' => '\\(',
            'T_PARENTHESIS_CLOSE' => '\\)',
            'T_BRACE_OPEN' => '\\{',
            'T_BRACE_CLOSE' => '\\}',
            'T_ATTR_OPEN' => '#\\[',
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
    'transitions' => [],
    'grammar' => [
        new \Phplrt\Parser\Grammar\Concatenation([6, 3, 7]),
        new \Phplrt\Parser\Grammar\Concatenation([3, 10]),
        new \Phplrt\Parser\Grammar\Alternation([0, 1]),
        new \Phplrt\Parser\Grammar\Alternation([11, 12, 13, 14, 15]),
        new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new \Phplrt\Parser\Grammar\Concatenation([4, 3]),
        new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new \Phplrt\Parser\Grammar\Repetition(5, 0, INF),
        new \Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new \Phplrt\Parser\Grammar\Concatenation([8, 3]),
        new \Phplrt\Parser\Grammar\Repetition(9, 0, INF),
        new \Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        new \Phplrt\Parser\Grammar\Alternation([21, 22, 23, 24, 25]),
        new \Phplrt\Parser\Grammar\Concatenation([2, 39]),
        new \Phplrt\Parser\Grammar\Concatenation([2, 43, 44]),
        new \Phplrt\Parser\Grammar\Alternation([17, 18, 19]),
        new \Phplrt\Parser\Grammar\Alternation([30, 31]),
        new \Phplrt\Parser\Grammar\Alternation([32, 33, 34]),
        new \Phplrt\Parser\Grammar\Alternation([35, 36, 37, 38]),
        new \Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        new \Phplrt\Parser\Grammar\Alternation([26, 27]),
        new \Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_PFX_FLOAT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_SFX_FLOAT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_EXP_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_BIN_INT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_OCT_INT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_HEX_INT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_DEC_INT_LITERAL', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        new \Phplrt\Parser\Grammar\Concatenation([3, 40]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        new \Phplrt\Parser\Grammar\Alternation([41, 3, 42]),
        new \Phplrt\Parser\Grammar\Concatenation([57, 58]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([46, 45]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        new \Phplrt\Parser\Grammar\Repetition(47, 0, INF),
        new \Phplrt\Parser\Grammar\Optional(48),
        new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        new \Phplrt\Parser\Grammar\Concatenation([49, 45, 50, 51, 52]),
        new \Phplrt\Parser\Grammar\Repetition(157, 1, INF),
        new \Phplrt\Parser\Grammar\Concatenation([16, 59]),
        new \Phplrt\Parser\Grammar\Concatenation([59]),
        new \Phplrt\Parser\Grammar\Optional(54),
        new \Phplrt\Parser\Grammar\Alternation([55, 56]),
        new \Phplrt\Parser\Grammar\Concatenation([177]),
        new \Phplrt\Parser\Grammar\Concatenation([67, 71, 72]),
        new \Phplrt\Parser\Grammar\Concatenation([109, 59]),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new \Phplrt\Parser\Grammar\Optional(60),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new \Phplrt\Parser\Grammar\Optional(61),
        new \Phplrt\Parser\Grammar\Concatenation([2, 62, 63, 64, 65]),
        new \Phplrt\Parser\Grammar\Concatenation([74]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([68, 67]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Repetition(69, 0, INF),
        new \Phplrt\Parser\Grammar\Optional(70),
        new \Phplrt\Parser\Grammar\Optional(54),
        new \Phplrt\Parser\Grammar\Concatenation([78, 79]),
        new \Phplrt\Parser\Grammar\Concatenation([95, 94]),
        new \Phplrt\Parser\Grammar\Alternation([83, 86, 88, 90, 80]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        new \Phplrt\Parser\Grammar\Alternation([75, 76]),
        new \Phplrt\Parser\Grammar\Optional(77),
        new \Phplrt\Parser\Grammar\Concatenation([28, 92]),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Concatenation([81, 82, 80]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new \Phplrt\Parser\Grammar\Concatenation([84, 85, 80]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Concatenation([87, 80]),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new \Phplrt\Parser\Grammar\Concatenation([89, 80]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Optional(91),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Concatenation([96, 97]),
        new \Phplrt\Parser\Grammar\Optional(93),
        new \Phplrt\Parser\Grammar\Concatenation([98, 108]),
        new \Phplrt\Parser\Grammar\Optional(28),
        new \Phplrt\Parser\Grammar\Concatenation([59]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new \Phplrt\Parser\Grammar\Optional(99),
        new \Phplrt\Parser\Grammar\Concatenation([100, 101]),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Optional(103),
        new \Phplrt\Parser\Grammar\Concatenation([104, 105]),
        new \Phplrt\Parser\Grammar\Alternation([102, 106]),
        new \Phplrt\Parser\Grammar\Optional(107),
        new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new \Phplrt\Parser\Grammar\Concatenation([125, 128]),
        new \Phplrt\Parser\Grammar\Concatenation([123, 124]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([112, 111]),
        new \Phplrt\Parser\Grammar\Optional(113),
        new \Phplrt\Parser\Grammar\Concatenation([110, 114]),
        new \Phplrt\Parser\Grammar\Optional(111),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        new \Phplrt\Parser\Grammar\Alternation([115, 116]),
        new \Phplrt\Parser\Grammar\Optional(117),
        new \Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        new \Phplrt\Parser\Grammar\Concatenation([118, 119, 120, 121]),
        new \Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new \Phplrt\Parser\Grammar\Optional(53),
        new \Phplrt\Parser\Grammar\Concatenation([131, 132]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([126, 125]),
        new \Phplrt\Parser\Grammar\Repetition(127, 0, INF),
        new \Phplrt\Parser\Grammar\Concatenation([133, 136, 137, 135]),
        new \Phplrt\Parser\Grammar\Concatenation([135]),
        new \Phplrt\Parser\Grammar\Optional(54),
        new \Phplrt\Parser\Grammar\Alternation([129, 130]),
        new \Phplrt\Parser\Grammar\Alternation([3, 23, 21]),
        new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        new \Phplrt\Parser\Grammar\Concatenation([59]),
        new \Phplrt\Parser\Grammar\Optional(134),
        new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new \Phplrt\Parser\Grammar\Alternation([53, 122]),
        new \Phplrt\Parser\Grammar\Optional(138),
        new \Phplrt\Parser\Grammar\Concatenation([2, 139]),
        new \Phplrt\Parser\Grammar\Concatenation([178]),
        new \Phplrt\Parser\Grammar\Optional(144),
        new \Phplrt\Parser\Grammar\Concatenation([141, 142]),
        new \Phplrt\Parser\Grammar\Concatenation([147, 148, 149, 59, 150, 59]),
        new \Phplrt\Parser\Grammar\Concatenation([28, 144]),
        new \Phplrt\Parser\Grammar\Alternation([143, 145]),
        new \Phplrt\Parser\Grammar\Alternation([151, 152, 153, 154, 155, 156]),
        new \Phplrt\Parser\Grammar\Alternation([59, 28]),
        new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_GTE', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_LTE', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', true),
        new \Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', true),
        new \Phplrt\Parser\Grammar\Concatenation([160, 158, 161, 162]),
        new \Phplrt\Parser\Grammar\Concatenation([163, 166]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_ATTR_OPEN', false),
        new \Phplrt\Parser\Grammar\Optional(159),
        new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        new \Phplrt\Parser\Grammar\Concatenation([2, 168]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([164, 163]),
        new \Phplrt\Parser\Grammar\Repetition(165, 0, INF),
        new \Phplrt\Parser\Grammar\Concatenation([173, 169, 174, 175, 176]),
        new \Phplrt\Parser\Grammar\Optional(167),
        new \Phplrt\Parser\Grammar\Concatenation([59]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Concatenation([170, 169]),
        new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new \Phplrt\Parser\Grammar\Repetition(171, 0, INF),
        new \Phplrt\Parser\Grammar\Optional(172),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new \Phplrt\Parser\Grammar\Concatenation([146]),
        new \Phplrt\Parser\Grammar\Concatenation([179, 182]),
        new \Phplrt\Parser\Grammar\Concatenation([183, 186]),
        new \Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        new \Phplrt\Parser\Grammar\Concatenation([180, 178]),
        new \Phplrt\Parser\Grammar\Optional(181),
        new \Phplrt\Parser\Grammar\Concatenation([187]),
        new \Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        new \Phplrt\Parser\Grammar\Concatenation([184, 179]),
        new \Phplrt\Parser\Grammar\Optional(185),
        new \Phplrt\Parser\Grammar\Alternation([190, 188]),
        new \Phplrt\Parser\Grammar\Concatenation([191, 193]),
        new \Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        new \Phplrt\Parser\Grammar\Concatenation([189, 188]),
        new \Phplrt\Parser\Grammar\Alternation([199, 29, 20, 66, 140]),
        new \Phplrt\Parser\Grammar\Concatenation([194, 195, 196]),
        new \Phplrt\Parser\Grammar\Repetition(192, 0, INF),
        new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        new \Phplrt\Parser\Grammar\Optional(59),
        new \Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new \Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new \Phplrt\Parser\Grammar\Concatenation([197, 59, 198]),
    ],
    'reducers' => [
        0 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\FullQualifiedName($children);
        },
        1 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Name($children);
        },
        3 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Identifier($children->getValue());
        },
        16 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Identifier($children->getValue());
        },
        17 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if ($this->literals === false) {
                throw FeatureNotAllowedException::fromFeature('literal values', $offset);
            }
            return $children;
        },
        18 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\ConstMaskNode($children[0]);
        },
        19 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        21 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return $this->stringPool[$token] ??= $children;
        },
        22 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\FloatLiteralNode::parse($token->getValue());
        },
        23 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return $this->integerPool[$token] ??= Node\Literal\IntLiteralNode::parse($token->getValue());
        },
        24 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\BoolLiteralNode::parse($token->getValue());
        },
        25 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Literal\NullLiteralNode($children->getValue());
        },
        28 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        29 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\VariableLiteralNode::parse($token->getValue());
        },
        30 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        31 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Node\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        45 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $hint = $attributes = null;

            if (\reset($children) instanceof Node\Stmt\Attribute\AttributeGroupsListNode) {
                if ($this->attributes === false) {
                    throw FeatureNotAllowedException::fromFeature('template argument attributes', $offset);
                }

                $attributes = \array_shift($children);
            }

            $type = \array_pop($children);

            if (\reset($children) !== false) {
                if ($this->hints === false) {
                    throw FeatureNotAllowedException::fromFeature('template argument hints', $offset);
                }

                $hint = \reset($children);
            }

            return new Node\Stmt\Template\TemplateArgumentNode(
                $type,
                $hint,
                $attributes,
            );
        },
        53 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if ($this->generics === false) {
                throw FeatureNotAllowedException::fromFeature('template arguments', $offset);
            }

            return new Node\Stmt\Template\TemplateArgumentsListNode($children);
        },
        54 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Attribute\AttributeGroupsListNode($children);
        },
        60 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\CallableParametersListNode($children);
        },
        66 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $name = \array_shift($children);

            if ($this->callables === false) {
                throw FeatureNotAllowedException::fromFeature('callable types', $offset);
            }

            $parameters = isset($children[0]) && $children[0] instanceof Node\Stmt\Callable\ParametersListNode
                ? \array_shift($children)
                : new Node\Stmt\Callable\CallableParametersListNode();

            return new Node\Stmt\CallableTypeNode(
                name: $name,
                parameters: $parameters,
                type: $children[0] ?? null,
            );
        },
        67 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \end($children);

            if ($children[0] instanceof Node\Stmt\Attribute\AttributeGroupsListNode) {
                if ($this->attributes === false) {
                    throw FeatureNotAllowedException::fromFeature('callable parameter attributes', $offset);
                }

                $result->attributes = $children[0];
            }

            return $result;
        },
        74 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 1) {
                return $children[0];
            }

            if ($children[0]->variadic) {
                throw SemanticException::fromVariadicWithDefault($offset);
            }

            $children[0]->optional = true;
            return $children[0];
        },
        75 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 1) {
                return $children[0];
            }

            if ($children[1]->variadic) {
                throw SemanticException::fromVariadicRedefinition($offset);
            }

            $children[1]->variadic = true;
            return $children[1];
        },
        76 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (!\is_array($children)) {
                return $children;
            }

            $result = \end($children);

            foreach ($children as $modifier) {
                if ($modifier instanceof \Phplrt\Contracts\Lexer\TokenInterface) {
                    switch ($modifier->getName()) {
                        case 'T_AMP':
                            $result->output = true;
                            break;
                        case 'T_ELLIPSIS':
                            if ($result->variadic) {
                                throw SemanticException::fromVariadicRedefinition($offset);
                            }
                            $result->variadic = true;
                            break;
                    }
                }
            }

            return $result;
        },
        80 => static function (\Phplrt\Parser\Context $ctx, $children) {
            $result = new Node\Stmt\Callable\CallableParameterNode(null, $children[0]);

            if (\count($children) !== 1) {
                $result->variadic = true;
            }

            return $result;
        },
        94 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
                return $children[0];
            }

            $children[0]->name = $children[1];
            return $children[0];
        },
        96 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \reset($children);

            foreach ($children as $modifier) {
                if ($modifier instanceof \Phplrt\Contracts\Lexer\TokenInterface) {
                    switch ($modifier->getName()) {
                        case 'T_AMP':
                            $result->output = true;
                            break;
                        case 'T_ELLIPSIS':
                            if ($result->variadic) {
                                throw SemanticException::fromVariadicRedefinition($offset);
                            }
                            $result->variadic = true;
                            break;
                    }
                }
            }

            return $result;
        },
        98 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Callable\CallableParameterNode($children[0]);
        },
        110 => static function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

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
        122 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

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
        125 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \end($children);

            if ($children[0] instanceof Node\Stmt\Attribute\AttributeGroupsListNode) {
                if ($this->attributes === false) {
                    throw FeatureNotAllowedException::fromFeature('shape field attributes', $offset);
                }

                $result->attributes = $children[0];
            }

            return $result;
        },
        129 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        130 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Shape\ImplicitFieldNode($children[0]);
        },
        140 => static function (\Phplrt\Parser\Context $ctx, $children) {
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
        146 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

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
                'T_GTE' => new Node\Stmt\Condition\GreaterOrEqualThanConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_ANGLE_BRACKET_CLOSE' => new Node\Stmt\Condition\GreaterThanConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_LTE' => new Node\Stmt\Condition\LessOrEqualThanConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_ANGLE_BRACKET_OPEN' => new Node\Stmt\Condition\LessThanConditionNode(
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
        157 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Attribute\AttributeGroupNode($children);
        },
        163 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Attribute\AttributeNode(
                $children[0],
            );
        },
        167 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Attribute\AttributeArgumentsListNode($children);
        },
        169 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new Node\Stmt\Attribute\AttributeArgumentNode($children[0]);
        },
        178 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 2) {
                if ($this->union === false) {
                    throw FeatureNotAllowedException::fromFeature('union types', $offset);
                }

                return new Node\Stmt\UnionTypeNode($children[0], $children[1]);
            }

            return $children;
        },
        179 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 2) {
                if ($this->intersection === false) {
                    throw FeatureNotAllowedException::fromFeature('intersection types', $offset);
                }

                return new Node\Stmt\IntersectionTypeNode($children[0], $children[1]);
            }

            return $children;
        },
        187 => static function (\Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
                return new Node\Stmt\NullableTypeNode($children[1]);
            }

            return $children;
        },
        188 => function (\Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $statement = \array_shift($children);

            foreach ($children as $child) {
                switch (true) {
                    // In case of list type
                    case $child === true:
                        if ($this->list === false) {
                            throw FeatureNotAllowedException::fromFeature('square bracket list types', $offset);
                        }

                        $statement = new Node\Stmt\TypesListNode($statement);
                        break;
                    // In case of offset access type
                    case $child instanceof Node\Stmt\TypeStatement:
                        if ($this->offsets === false) {
                            throw FeatureNotAllowedException::fromFeature('type offsets', $offset);
                        }

                        $statement = new Node\Stmt\TypeOffsetAccessNode($statement, $child);
                        break;
                    default:
                        throw new SemanticException($offset, \sprintf(
                            'Internal error, unexpected square bracket sub-node %s',
                            \get_debug_type($child),
                        ));
                }
            }

            return $statement;
        },
        192 => static function (\Phplrt\Parser\Context $ctx, $children) {
            return $children[1] ?? true;
        },
    ],
];