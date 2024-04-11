<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;

trait InteractWithCommentsParser
{
    use InteractWithPhpParser;

    /**
     * @param array<array-key, Stmt> $statements
     * @return iterable<array-key, non-empty-string>
     * @throws \LogicException
     */
    protected static function getCommentsFromStatements(array $statements): iterable
    {
        $comments = new \ArrayObject();
        $traverser = new NodeTraverser();

        $traverser->addVisitor(new class ($comments) extends NodeVisitorAbstract {
            public function __construct(
                private readonly \ArrayObject $comments,
            ) {}

            public function enterNode(Node $node): void
            {
                $comment = $node->getDocComment();

                if ($comment === null) {
                    return;
                }

                $this->comments['line ' . $node->getLine()] = $comment->getText();
            }
        });

        $traverser->traverse($statements);

        return $comments->getIterator();
    }

    /**
     * @return iterable<array-key, non-empty-string>
     * @throws \LogicException
     * @throws ExpectationFailedException
     */
    protected static function getCommentsFromPhpCode(string $code): iterable
    {
        return self::getCommentsFromStatements(
            statements: self::getStatementsFromPhpCode($code),
        );
    }

    /**
     * @param non-empty-string $pathname
     * @return iterable<array-key, non-empty-string>
     * @throws ExpectationFailedException
     * @throws \LogicException
     * @throws AssertionFailedError
     */
    protected static function getCommentsFromPhpFile(string $pathname): iterable
    {
        return self::getCommentsFromStatements(
            statements: self::getStatementsFromPhpFile($pathname),
        );
    }
}
