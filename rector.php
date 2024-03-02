<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $config): void {
    $config->paths([__DIR__ . '/src']);
    $config->cacheDirectory(__DIR__ . '/vendor/.cache.rector');

    $config->sets([
        LevelSetList::UP_TO_PHP_81,
    ]);
};
