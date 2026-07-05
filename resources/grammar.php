<?php

declare(strict_types=1);

use TypeLang\Parser\Exception;
use TypeLang\Type;

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
            'T_PFX_FLOAT_LITERAL' => '\\-?[0-9]++\\.[0-9]*+(?:[eE]-?[0-9]++)?',
            'T_SFX_FLOAT_LITERAL' => '\\-?[0-9]*+\\.[0-9]++(?:[eE]-?[0-9]++)?',
            'T_EXP_LITERAL' => '\\-?[0-9]++[eE]-?[0-9]++',
            'T_BIN_INT_LITERAL' => '\\-?0[bB][01_]++',
            'T_OCT_INT_LITERAL' => '\\-?0[oO][0-7_]++',
            'T_HEX_INT_LITERAL' => '\\-?0[xX][0-9a-fA-F_]++',
            'T_DEC_INT_LITERAL' => '\\-?[0-9][0-9_]*+',
            'T_BOOL_LITERAL' => '(?i)(?:true|false)(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NULL_LITERAL' => '(?i)null(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_NEQ' => '(?i)is\\h++not(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_EQ' => '(?i)is(?![a-zA-Z0-9\\-_\\x80-\\xff])',
            'T_THIS' => '\\$this\\b',
            'T_VARIABLE' => '\\$[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*+',
            'T_NAME_WITH_SPACE' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*+\\s++',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9\\-_\\x80-\\xff]*+',
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
            'T_DOUBLE_COLON' => '::',
            'T_COLON' => ':',
            'T_ASSIGN' => '=',
            'T_NS_DELIMITER' => '\\\\',
            'T_QMARK' => '\\?',
            'T_OR' => '\\|',
            'T_AMP' => '&',
            'T_ASTERISK' => '\\*',
            'T_COMMENT' => '(?://|#)[^\\r\\n]*+',
            'T_DOC_COMMENT' => '/\\*.*?\\*/',
            'T_WHITESPACE' => '\\s++',
        ],
    ],
    'skip' => [
        'T_COMMENT',
        'T_DOC_COMMENT',
        'T_WHITESPACE',
    ],
    'transitions' => [],
    'grammar' => [
        new Phplrt\Parser\Grammar\Concatenation([6, 3, 7]),
        new Phplrt\Parser\Grammar\Concatenation([3, 10]),
        new Phplrt\Parser\Grammar\Alternation([0, 1]),
        new Phplrt\Parser\Grammar\Alternation([11, 12, 13, 14, 15]),
        new Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new Phplrt\Parser\Grammar\Concatenation([4, 3]),
        new Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new Phplrt\Parser\Grammar\Repetition(5, 0, INF),
        new Phplrt\Parser\Grammar\Lexeme('T_NS_DELIMITER', false),
        new Phplrt\Parser\Grammar\Concatenation([8, 3]),
        new Phplrt\Parser\Grammar\Repetition(9, 0, INF),
        new Phplrt\Parser\Grammar\Lexeme('T_NAME', true),
        new Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        new Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        new Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_NAME_WITH_SPACE', true),
        new Phplrt\Parser\Grammar\Alternation([21, 22, 23, 24, 25]),
        new Phplrt\Parser\Grammar\Concatenation([2, 39]),
        new Phplrt\Parser\Grammar\Concatenation([2, 43, 44]),
        new Phplrt\Parser\Grammar\Alternation([17, 18, 19]),
        new Phplrt\Parser\Grammar\Alternation([30, 31]),
        new Phplrt\Parser\Grammar\Alternation([32, 33, 34]),
        new Phplrt\Parser\Grammar\Alternation([35, 36, 37, 38]),
        new Phplrt\Parser\Grammar\Lexeme('T_BOOL_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_NULL_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_VARIABLE', true),
        new Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        new Phplrt\Parser\Grammar\Alternation([26, 27]),
        new Phplrt\Parser\Grammar\Lexeme('T_THIS', true),
        new Phplrt\Parser\Grammar\Lexeme('T_DQ_STRING_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_SQ_STRING_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_PFX_FLOAT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_SFX_FLOAT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_EXP_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_BIN_INT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_OCT_INT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_HEX_INT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_DEC_INT_LITERAL', true),
        new Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', false),
        new Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        new Phplrt\Parser\Grammar\Concatenation([3, 40]),
        new Phplrt\Parser\Grammar\Lexeme('T_ASTERISK', true),
        new Phplrt\Parser\Grammar\Lexeme('T_DOUBLE_COLON', false),
        new Phplrt\Parser\Grammar\Alternation([41, 3, 42]),
        new Phplrt\Parser\Grammar\Concatenation([57, 58]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([46, 45]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', false),
        new Phplrt\Parser\Grammar\Repetition(47, 0, INF),
        new Phplrt\Parser\Grammar\Optional(48),
        new Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', false),
        new Phplrt\Parser\Grammar\Concatenation([49, 45, 50, 51, 52]),
        new Phplrt\Parser\Grammar\Repetition(155, 1, INF),
        new Phplrt\Parser\Grammar\Concatenation([16, 59]),
        new Phplrt\Parser\Grammar\Concatenation([59]),
        new Phplrt\Parser\Grammar\Optional(54),
        new Phplrt\Parser\Grammar\Alternation([55, 56]),
        new Phplrt\Parser\Grammar\Concatenation([175]),
        new Phplrt\Parser\Grammar\Concatenation([67, 71, 72]),
        new Phplrt\Parser\Grammar\Concatenation([107, 59]),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new Phplrt\Parser\Grammar\Optional(60),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new Phplrt\Parser\Grammar\Optional(61),
        new Phplrt\Parser\Grammar\Concatenation([2, 62, 63, 64, 65]),
        new Phplrt\Parser\Grammar\Concatenation([74]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([68, 67]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Repetition(69, 0, INF),
        new Phplrt\Parser\Grammar\Optional(70),
        new Phplrt\Parser\Grammar\Optional(54),
        new Phplrt\Parser\Grammar\Concatenation([78, 79]),
        new Phplrt\Parser\Grammar\Concatenation([93, 92]),
        new Phplrt\Parser\Grammar\Alternation([83, 86, 88, 90, 80]),
        new Phplrt\Parser\Grammar\Lexeme('T_ASSIGN', true),
        new Phplrt\Parser\Grammar\Alternation([75, 76]),
        new Phplrt\Parser\Grammar\Optional(77),
        new Phplrt\Parser\Grammar\Concatenation([28]),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Concatenation([81, 82, 80]),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new Phplrt\Parser\Grammar\Concatenation([84, 85, 80]),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Concatenation([87, 80]),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new Phplrt\Parser\Grammar\Concatenation([89, 80]),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Concatenation([94, 95]),
        new Phplrt\Parser\Grammar\Optional(91),
        new Phplrt\Parser\Grammar\Concatenation([96, 106]),
        new Phplrt\Parser\Grammar\Optional(28),
        new Phplrt\Parser\Grammar\Concatenation([59]),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new Phplrt\Parser\Grammar\Optional(97),
        new Phplrt\Parser\Grammar\Concatenation([98, 99]),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', true),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Optional(101),
        new Phplrt\Parser\Grammar\Concatenation([102, 103]),
        new Phplrt\Parser\Grammar\Alternation([100, 104]),
        new Phplrt\Parser\Grammar\Optional(105),
        new Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new Phplrt\Parser\Grammar\Concatenation([123, 126]),
        new Phplrt\Parser\Grammar\Concatenation([121, 122]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([110, 109]),
        new Phplrt\Parser\Grammar\Optional(111),
        new Phplrt\Parser\Grammar\Concatenation([108, 112]),
        new Phplrt\Parser\Grammar\Optional(109),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Lexeme('T_BRACE_OPEN', false),
        new Phplrt\Parser\Grammar\Alternation([113, 114]),
        new Phplrt\Parser\Grammar\Optional(115),
        new Phplrt\Parser\Grammar\Lexeme('T_BRACE_CLOSE', false),
        new Phplrt\Parser\Grammar\Concatenation([116, 117, 118, 119]),
        new Phplrt\Parser\Grammar\Lexeme('T_ELLIPSIS', true),
        new Phplrt\Parser\Grammar\Optional(53),
        new Phplrt\Parser\Grammar\Concatenation([129, 130]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([124, 123]),
        new Phplrt\Parser\Grammar\Repetition(125, 0, INF),
        new Phplrt\Parser\Grammar\Concatenation([131, 134, 135, 133]),
        new Phplrt\Parser\Grammar\Concatenation([133]),
        new Phplrt\Parser\Grammar\Optional(54),
        new Phplrt\Parser\Grammar\Alternation([127, 128]),
        new Phplrt\Parser\Grammar\Alternation([18, 19, 3, 23, 21]),
        new Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        new Phplrt\Parser\Grammar\Concatenation([59]),
        new Phplrt\Parser\Grammar\Optional(132),
        new Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new Phplrt\Parser\Grammar\Alternation([53, 120]),
        new Phplrt\Parser\Grammar\Optional(136),
        new Phplrt\Parser\Grammar\Concatenation([2, 137]),
        new Phplrt\Parser\Grammar\Concatenation([176]),
        new Phplrt\Parser\Grammar\Optional(142),
        new Phplrt\Parser\Grammar\Concatenation([139, 140]),
        new Phplrt\Parser\Grammar\Concatenation([145, 146, 147, 59, 148, 59]),
        new Phplrt\Parser\Grammar\Concatenation([28, 142]),
        new Phplrt\Parser\Grammar\Alternation([141, 143]),
        new Phplrt\Parser\Grammar\Alternation([149, 150, 151, 152, 153, 154]),
        new Phplrt\Parser\Grammar\Alternation([59, 28]),
        new Phplrt\Parser\Grammar\Lexeme('T_QMARK', false),
        new Phplrt\Parser\Grammar\Lexeme('T_COLON', false),
        new Phplrt\Parser\Grammar\Lexeme('T_EQ', true),
        new Phplrt\Parser\Grammar\Lexeme('T_NEQ', true),
        new Phplrt\Parser\Grammar\Lexeme('T_GTE', true),
        new Phplrt\Parser\Grammar\Lexeme('T_LTE', true),
        new Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_OPEN', true),
        new Phplrt\Parser\Grammar\Lexeme('T_ANGLE_BRACKET_CLOSE', true),
        new Phplrt\Parser\Grammar\Concatenation([158, 156, 159, 160]),
        new Phplrt\Parser\Grammar\Concatenation([161, 164]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Lexeme('T_ATTR_OPEN', false),
        new Phplrt\Parser\Grammar\Optional(157),
        new Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        new Phplrt\Parser\Grammar\Concatenation([2, 166]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([162, 161]),
        new Phplrt\Parser\Grammar\Repetition(163, 0, INF),
        new Phplrt\Parser\Grammar\Concatenation([171, 167, 172, 173, 174]),
        new Phplrt\Parser\Grammar\Optional(165),
        new Phplrt\Parser\Grammar\Concatenation([59]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Concatenation([168, 167]),
        new Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new Phplrt\Parser\Grammar\Repetition(169, 0, INF),
        new Phplrt\Parser\Grammar\Optional(170),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new Phplrt\Parser\Grammar\Concatenation([144]),
        new Phplrt\Parser\Grammar\Concatenation([177, 180]),
        new Phplrt\Parser\Grammar\Concatenation([181, 184]),
        new Phplrt\Parser\Grammar\Lexeme('T_OR', false),
        new Phplrt\Parser\Grammar\Concatenation([178, 176]),
        new Phplrt\Parser\Grammar\Optional(179),
        new Phplrt\Parser\Grammar\Concatenation([185]),
        new Phplrt\Parser\Grammar\Lexeme('T_AMP', false),
        new Phplrt\Parser\Grammar\Concatenation([182, 177]),
        new Phplrt\Parser\Grammar\Optional(183),
        new Phplrt\Parser\Grammar\Alternation([188, 186]),
        new Phplrt\Parser\Grammar\Concatenation([189, 191]),
        new Phplrt\Parser\Grammar\Lexeme('T_QMARK', true),
        new Phplrt\Parser\Grammar\Concatenation([187, 186]),
        new Phplrt\Parser\Grammar\Alternation([197, 29, 20, 66, 138]),
        new Phplrt\Parser\Grammar\Concatenation([192, 193, 194]),
        new Phplrt\Parser\Grammar\Repetition(190, 0, INF),
        new Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_OPEN', true),
        new Phplrt\Parser\Grammar\Optional(59),
        new Phplrt\Parser\Grammar\Lexeme('T_SQUARE_BRACKET_CLOSE', false),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_OPEN', false),
        new Phplrt\Parser\Grammar\Lexeme('T_PARENTHESIS_CLOSE', false),
        new Phplrt\Parser\Grammar\Concatenation([195, 59, 196]),
    ],
    'reducers' => [
        0 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Name($children, true);
        },
        1 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Name($children, false);
        },
        3 => static function (Phplrt\Parser\Context $ctx, $children) {
            return Type\Identifier::createFromString($children->getValue());
        },
        16 => static function (Phplrt\Parser\Context $ctx, $children) {
            return Type\Identifier::createFromString($children->getValue());
        },
        17 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if ($this->features->literals === false) {
                throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('literal values', $offset);
            }

            return $children;
        },
        18 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\ConstMaskNode($children[0]);
        },
        19 => static function (Phplrt\Parser\Context $ctx, $children) {
            // <ClassName> :: <ConstPrefix> "*"
            if (\count($children) === 3) {
                return new Type\ClassConstMaskNode(
                    $children[0],
                    $children[1],
                );
            }

            // <ClassName> :: <ConstName>
            if ($children[1] instanceof Type\Identifier) {
                return new Type\ClassConstNode(
                    $children[0],
                    $children[1],
                );
            }

            // <ClassName> :: "*"
            return new Type\ClassConstMaskNode($children[0]);
        },
        21 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return $this->stringPool[$token] ??= $children;
        },
        22 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\FloatLiteralNode::parse($token->getValue());
        },
        23 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return $this->integerPool[$token] ??= Type\Literal\IntLiteralNode::parse($token->getValue());
        },
        24 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\BoolLiteralNode::parse($token->getValue());
        },
        25 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Literal\NullLiteralNode($children->getValue());
        },
        28 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\VariableLiteralNode::parse($token->getValue());
        },
        29 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\VariableLiteralNode::parse($token->getValue());
        },
        30 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\StringLiteralNode::createFromDoubleQuotedString($token->getValue());
        },
        31 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$token" variable is an auto-generated
            $token = $ctx->lastProcessedToken;

            return Type\Literal\StringLiteralNode::createFromSingleQuotedString($token->getValue());
        },
        45 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $hint = $attributes = null;

            if (\reset($children) instanceof Type\Attribute\AttributeGroupListNode) {
                if ($this->features->attributes === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('template argument attributes', $offset);
                }

                $attributes = \array_shift($children);
            }

            $type = \array_pop($children);

            if (\reset($children) !== false) {
                if ($this->features->hints === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('template argument hints', $offset);
                }

                $hint = \reset($children);
            }

            return new Type\Template\TemplateArgumentNode(
                $type,
                $hint,
                $attributes,
            );
        },
        53 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if ($this->features->generics === false) {
                throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('template arguments', $offset);
            }

            return new Type\Template\TemplateArgumentListNode($children);
        },
        54 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Attribute\AttributeGroupListNode($children);
        },
        60 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Callable\CallableParameterListNode($children);
        },
        66 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $name = \array_shift($children);

            if ($this->features->callables === false) {
                throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('callable types', $offset);
            }

            $parameters = isset($children[0]) && $children[0] instanceof Type\Callable\CallableParameterListNode
                ? \array_shift($children)
                : new Type\Callable\CallableParameterListNode();

            return new Type\CallableTypeNode(
                name: $name,
                parameters: $parameters,
                type: $children[0] ?? null,
            );
        },
        67 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \end($children);

            if ($children[0] instanceof Type\Attribute\AttributeGroupListNode) {
                if ($this->features->attributes === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('callable parameter attributes', $offset);
                }

                $result->attributes = $children[0];
            }

            return $result;
        },
        74 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 1) {
                return $children[0];
            }

            if ($children[0]->isVariadic) {
                throw Exception\VariadicWithDefaultException::becauseVariadicHasDefault($offset);
            }

            $children[0]->isOptional = true;

            return $children[0];
        },
        75 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 1) {
                return $children[0];
            }

            if ($children[1]->isVariadic) {
                throw Exception\VariadicRedefinitionException::becauseVariadicIsRedefined($offset);
            }

            $children[1]->isVariadic = true;

            return $children[1];
        },
        76 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (!\is_array($children)) {
                return $children;
            }

            $result = \end($children);

            foreach ($children as $modifier) {
                if ($modifier instanceof Phplrt\Contracts\Lexer\TokenInterface) {
                    switch ($modifier->getName()) {
                        case 'T_AMP':
                            $result->isOutput = true;
                            break;
                        case 'T_ELLIPSIS':
                            if ($result->isVariadic) {
                                throw Exception\VariadicRedefinitionException::becauseVariadicIsRedefined($offset);
                            }
                            $result->isVariadic = true;
                            break;
                    }
                }
            }

            return $result;
        },
        80 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Callable\CallableParameterNode(null, $children[0]);
        },
        92 => static function (Phplrt\Parser\Context $ctx, $children) {
            if (\count($children) === 1) {
                return $children[0];
            }

            $children[0]->name = $children[1];

            return $children[0];
        },
        94 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \reset($children);

            foreach ($children as $modifier) {
                if ($modifier instanceof Phplrt\Contracts\Lexer\TokenInterface) {
                    switch ($modifier->getName()) {
                        case 'T_AMP':
                            $result->isOutput = true;
                            break;
                        case 'T_ELLIPSIS':
                            if ($result->isVariadic) {
                                throw Exception\VariadicRedefinitionException::becauseVariadicIsRedefined($offset);
                            }
                            $result->isVariadic = true;
                            break;
                    }
                }
            }

            return $result;
        },
        96 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Callable\CallableParameterNode($children[0]);
        },
        108 => static function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $explicit = [];
            $implicit = false;

            foreach ($children as $field) {
                if ($field instanceof Type\Shape\ExplicitFieldNode) {
                    $key = $field->index;

                    if (\in_array($key, $explicit, true)) {
                        throw Exception\ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated($key, $field->offset);
                    }

                    $explicit[] = $key;
                } else {
                    $implicit = true;
                }
            }

            if ($explicit !== [] && $implicit) {
                throw Exception\ShapeKeysMixingException::becauseShapeKeysAreMixed($offset);
            }

            return new Type\Shape\FieldsListNode($children);
        },
        120 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if ($children === []) {
                return new Type\Shape\FieldsListNode();
            }

            if ($this->features->shapes === false) {
                throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('shape fields', $offset);
            }

            $parameters = null;

            if (\end($children) instanceof Type\Template\TemplateArgumentListNode) {
                $parameters = \array_pop($children);
            }

            $fields = \reset($children) instanceof Type\Shape\FieldsListNode
                ? \array_shift($children)
                : new Type\Shape\FieldsListNode();

            if ($children !== []) {
                $fields->sealed = false;
            }

            return \array_filter([$parameters, $fields]);
        },
        123 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $result = \end($children);

            if ($children[0] instanceof Type\Attribute\AttributeGroupListNode) {
                if ($this->features->attributes === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('shape field attributes', $offset);
                }

                $result->attributes = $children[0];
            }

            return $result;
        },
        127 => static function (Phplrt\Parser\Context $ctx, $children) {
            $name = $children[0];
            $value = \array_pop($children);

            // In case of "nullable" suffix defined
            $optional = \count($children) === 2;

            return match (true) {
                $name instanceof Type\Literal\IntLiteralNode
                    => new Type\Shape\NumericFieldNode($name, $value, $optional),
                $name instanceof Type\Literal\StringLiteralNode
                    => new Type\Shape\StringNamedFieldNode($name, $value, $optional),
                $name instanceof Type\ClassConstNode
                    => new Type\Shape\ClassConstFieldNode($name, $value, $optional),
                $name instanceof Type\ClassConstMaskNode
                    => new Type\Shape\ClassConstMaskFieldNode($name, $value, $optional),
                $name instanceof Type\ConstMaskNode
                    => new Type\Shape\ConstMaskFieldNode($name, $value, $optional),
                default => new Type\Shape\NamedFieldNode($name, $value, $optional),
            };
        },
        128 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Shape\ImplicitFieldNode($children[0]);
        },
        138 => static function (Phplrt\Parser\Context $ctx, $children) {
            $fields = $parameters = null;

            // Shape fields
            if (\end($children) instanceof Type\Shape\FieldsListNode) {
                $fields = \array_pop($children);
            }

            // Template parameters
            if (\end($children) instanceof Type\Template\TemplateArgumentListNode) {
                $parameters = \array_pop($children);
            }

            return new Type\NamedTypeNode(
                $children[0],
                $parameters,
                $fields,
            );
        },
        144 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $count = \count($children);

            if ($count === 1) {
                return $children[0];
            }

            if ($this->features->conditions === false) {
                throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('conditional expressions', $offset);
            }

            $condition = match ($children[1]->getName()) {
                'T_EQ' => new Type\Condition\EqualConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_NEQ' => new Type\Condition\NotEqualConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_GTE' => new Type\Condition\GreaterThanOrEqualConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_ANGLE_BRACKET_CLOSE' => new Type\Condition\GreaterThanConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_LTE' => new Type\Condition\LessThanOrEqualConditionNode(
                    $children[0],
                    $children[2],
                ),
                'T_ANGLE_BRACKET_OPEN' => new Type\Condition\LessThanConditionNode(
                    $children[0],
                    $children[2],
                ),
                default => throw Exception\InvalidConditionalOperatorException::becauseConditionalOperatorIsInvalid(
                    $children[1]->getValue(),
                    $offset,
                ),
            };

            return new Type\TernaryExpressionNode(
                $condition,
                $children[3],
                $children[4],
            );
        },
        155 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Attribute\AttributeGroupNode($children);
        },
        161 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Attribute\AttributeNode(
                $children[0],
            );
        },
        165 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Attribute\AttributeArgumentListNode($children);
        },
        167 => static function (Phplrt\Parser\Context $ctx, $children) {
            return new Type\Attribute\AttributeArgumentNode($children[0]);
        },
        176 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 2) {
                if ($this->features->unions === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('union types', $offset);
                }

                return new Type\UnionTypeNode($children[0], $children[1]);
            }

            return $children;
        },
        177 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            if (\count($children) === 2) {
                if ($this->features->intersections === false) {
                    throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('intersection types', $offset);
                }

                return new Type\IntersectionTypeNode($children[0], $children[1]);
            }

            return $children;
        },
        185 => static function (Phplrt\Parser\Context $ctx, $children) {
            if (\is_array($children)) {
                return new Type\NullableTypeNode($children[1]);
            }

            return $children;
        },
        186 => function (Phplrt\Parser\Context $ctx, $children) {
            // The "$offset" variable is an auto-generated
            $offset = $ctx->lastProcessedToken->getOffset();

            $statement = \array_shift($children);

            foreach ($children as $child) {
                switch (true) {
                    // In case of list type
                    case $child === true:
                        if ($this->features->lists === false) {
                            throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('square bracket list types', $offset);
                        }

                        $statement = new Type\TypesListNode($statement);
                        break;
                        // In case of offset access type
                    case $child instanceof Type\TypeNode:
                        if ($this->features->offsets === false) {
                            throw Exception\FeatureNotAllowedException::becauseFeatureIsNotAllowed('type offsets', $offset);
                        }

                        $statement = new Type\TypeOffsetAccessNode($statement, $child);
                        break;
                    default:
                        throw Exception\InternalSemanticException::becauseSubNodeIsUnexpected(
                            \get_debug_type($child),
                            $offset,
                        );
                }
            }

            return $statement;
        },
        190 => static function (Phplrt\Parser\Context $ctx, $children) {
            return $children[1] ?? true;
        },
    ],
];
