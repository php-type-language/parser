<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional;

use TypeLang\Parser\Tests\Concern\InteractWithDocBlocks;
use TypeLang\Parser\Tests\TestCase as BaseTestCase;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Source\File;
use PHPUnit\Framework\Attributes\Group;

#[Group('functional')]
class TestCase extends BaseTestCase
{
    use InteractWithDocBlocks;

    /**
     * @psalm-taint-sink file $directory
     * @param non-empty-string $directory
     *
     * @return list<ReadableInterface>
     */
    protected static function getSources(string $directory, array $ext = ['php', 'stub', 'phpstub', 'stubphp']): iterable
    {
        $result = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($result as $file) {
            if (!\in_array(\strtolower($file->getExtension()), $ext, true)) {
                continue;
            }

            yield File::fromSplFileInfo($file);
        }
    }
}
