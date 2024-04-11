<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PhpParser\Node\Stmt;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use TypeLang\PHPDoc\DocBlock;
use TypeLang\PHPDoc\Parser;
use TypeLang\PHPDoc\ParserInterface;
use TypeLang\PHPDoc\ParserInterface as PHPDocParserInterface;
use TypeLang\PHPDoc\Tag\Factory\PrefixedTagFactory;
use TypeLang\PHPDoc\Standard;

trait InteractWithPHPDocParser
{
    use InteractWithCommentsParser;

    private static ?ParserInterface $phpdoc = null;

    /**
     * @param iterable<array-key, non-empty-string> $comments
     * @return iterable<array-key, DocBlock>
     */
    protected static function getDocBlocksFromComments(iterable $comments): iterable
    {
        $parser = self::getPHPDocParser();

        foreach ($comments as $info => $comment) {
            yield $info => $parser->parse($comment);
        }
    }

    /**
     * @param array<array-key, Stmt> $statements
     * @return iterable<array-key, DocBlock>
     * @throws \LogicException
     */
    protected static function getDocBlocksFromStatements(array $statements): iterable
    {
        return self::getDocBlocksFromComments(
            comments: self::getCommentsFromStatements(
                statements: $statements,
            ),
        );
    }

    /**
     * @return iterable<array-key, DocBlock>
     * @throws \LogicException
     * @throws ExpectationFailedException
     */
    protected static function getDocBlocksFromPhpCode(string $code): iterable
    {
        return self::getDocBlocksFromComments(
            comments: self::getCommentsFromPhpCode(
                code: $code,
            ),
        );
    }

    /**
     * @param non-empty-string $pathname
     * @return iterable<array-key, DocBlock>
     * @throws ExpectationFailedException
     * @throws \LogicException
     * @throws AssertionFailedError
     */
    protected static function getDocBlocksFromPhpFile(string $pathname): iterable
    {
        return self::getDocBlocksFromComments(
            comments: self::getCommentsFromPhpFile(
                pathname: $pathname,
            ),
        );
    }

    /**
     * @return PHPDocParserInterface
     */
    private static function getPHPDocParser(): PHPDocParserInterface
    {
        if (self::$phpdoc !== null) {
            return self::$phpdoc;
        }

        $factory = new PrefixedTagFactory(self::getPrefixes());
        $factory->register('method', new Standard\MethodTagFactory());
        $factory->register(['param', 'param-out'], new Standard\ParamTagFactory());
        $factory->register('property', new Standard\PropertyTagFactory());
        $factory->register('property-read', new Standard\PropertyReadTagFactory());
        $factory->register('property-write', new Standard\PropertyWriteTagFactory());
        $factory->register('return', new Standard\ReturnTagFactory());
        $factory->register('throws', new Standard\ThrowsTagFactory());
        $factory->register('var', new Standard\VarTagFactory());

        return self::$phpdoc = new Parser($factory);
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    protected static function getPrefixes(): array
    {
        return ['psalm-', 'phpstan-'];
    }

    /**
     * @param non-empty-string $tag
     * @return non-empty-list<non-empty-string>
     */
    protected static function getPrefixedTags(string $tag): array
    {
        $result = [$tag];

        foreach (self::getPrefixes() as $prefix) {
            $result[] = $prefix . $tag;
        }

        return $result;
    }
}
