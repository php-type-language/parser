<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use TypeLang\PHPDoc\DocBlock;
use TypeLang\PHPDoc\Tag\TagInterface;
use TypeLang\PHPDoc\Tag\TagsProviderInterface;

trait InteractWithPHPDocTagsParser
{
    use InteractWithPHPDocParser;

    /**
     * @return iterable<array-key, TagInterface>
     */
    protected static function getTagsFromDocBlock(DocBlock $docBlock): iterable
    {
        $description = $docBlock->getDescription();

        if ($description instanceof TagsProviderInterface) {
            yield from self::getTagsFromTagsProvider($description);
        }

        yield from self::getTagsFromTagsProvider($docBlock);
    }

    /**
     * @return iterable<array-key, TagInterface>
     */
    private static function getTagsFromTagsProvider(TagsProviderInterface $provider): iterable
    {
        foreach ($provider->getTags() as $tag) {
            yield $tag;

            if ($tag instanceof TagsProviderInterface) {
                yield from self::getTagsFromTagsProvider($tag);
            }

            $description = $tag->getDescription();

            if ($description instanceof TagsProviderInterface) {
                yield from self::getTagsFromTagsProvider($description);
            }
        }
    }
}
