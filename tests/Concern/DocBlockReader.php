<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Position\Position;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class DocBlockReader
{
    private readonly Parser $php;

    public function __construct()
    {
        $this->php = (new ParserFactory())
            ->create(ParserFactory::ONLY_PHP7)
        ;
    }

    /**
     * @param list<non-empty-string> $tags
     *
     * @return DocBlockFactory
     */
    private function getDocBlockFactory(array $tags): DocBlockFactory
    {
        $fqsenResolver      = new FqsenResolver();
        $tagFactory         = new StandardTagFactory($fqsenResolver);
        $descriptionFactory = new DescriptionFactory($tagFactory);

        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        $factory = new DocBlockFactory($descriptionFactory, $tagFactory);

        foreach ($tags as $tag) {
            $factory->registerTagHandler($tag, Generic::class);
        }

        return $factory;
    }

    /**
     * @param list<non-empty-string> $tags
     *
     * @return iterable<non-empty-string, PositionInterface>
     */
    public function read(ReadableInterface $source, array $tags = []): iterable
    {
        $factory = $this->getDocBlockFactory($tags);

        foreach ($this->getPhpNodes($source) as $node) {
            $position = Position::fromOffset($source, $node->getStartFilePos());
            $phpdoc = $factory->create($node->getText() ?? '');

            foreach ($phpdoc->getTags() as $tag) {
                $expected = $tag instanceof Generic && \in_array($tag->getName(), $tags, true);

                if (!$expected) {
                    continue;
                }

                $description = $tag->getDescription()
                    ?->getBodyTemplate();

                if ($description === null) {
                    continue;
                }

                // Ignore tags without typehint
                if (\str_starts_with($description, '$')) {
                    continue;
                }

                yield $this->extractType($description) => $position;
            }
        }
    }

    /**
     * @param ReadableInterface $source
     *
     * @return iterable<Doc>
     */
    private function getPhpNodes(ReadableInterface $source): iterable
    {
        $ast = $this->php->parse($source->getContents());

        $result = (new NodeFinder())
            ->find($ast, static function (Node $node): bool {
                return $node->getDocComment() !== null;
            });

        foreach ($result as $node) {
            if ($phpdoc = $node->getDocComment()) {
                yield $phpdoc;
            }
        }
    }


    /**
     * @param string $body
     *
     * @return string
     */
    private function extractType(string $body): string
    {
        $type = '';
        $nesting = [];
        $allowWhitespace = false;

        for ($i = 0, $length = \strlen($body); $i < $length; ++$i) {
            $char = $body[$i];
            $whitespace = \ctype_space($char);

            if ($char === ':' || $char === '|' || $char === '&') {
                $allowWhitespace = true;
            } elseif (!$whitespace) {
                $allowWhitespace = false;
            }

            if ($nesting === [] && $whitespace && !$allowWhitespace) {
                break;
            }

            $type .= $char;

            if (\in_array($char, ['<', '(', '[', '{'], true)) {
                $nesting[] = $char;
            }

            if (($char === '>' && \end($nesting) === '<')
                || ($char === ')' && \end($nesting) === '(')
                || ($char === ']' && \end($nesting) === '[')
                || ($char === '}' && \end($nesting) === '{')
            ) {
                \array_pop($nesting);
            }
        }

        return $type;
    }
}
