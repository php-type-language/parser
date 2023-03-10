<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests\Feature;

use Hyper\Parser\Tests\Concern\InteractWithDocBlocks;
use Hyper\Parser\Tests\TestCase as BaseTestCase;
use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\FileInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Position\Position;
use Phplrt\Source\File;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser'), Group('feature')]
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

    protected static function location(ReadableInterface $src, PositionInterface $pos = null): string
    {
        $name = '<source#' . $src->getHash() . '>';

        if ($src instanceof FileInterface) {
            $name = \realpath($src->getPathname()) ?: $src->getPathname();
        }

        return $name . ':' . ($pos ?? Position::start())->getLine();
    }
}
