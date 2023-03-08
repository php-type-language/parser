<?php

declare(strict_types=1);

namespace Hyper\Type\Factory;

trait Singleton
{
    /**
     * @var array<class-string<static>, static>
     */
    private static array $instances = [];

    public static function getInstance(): static
    {
        if (isset(self::$instances[static::class])) {
            return self::$instances[static::class];
        }

        $reflection = new \ReflectionClass(static::class);

        try {
            return self::$instances[static::class] = $reflection->newInstanceWithoutConstructor();
        } finally {
            if ($reflection->hasMethod('__construct')) {
                $construct = $reflection->getMethod('__construct');
                $construct->invoke(self::$instances[static::class]);
            }
        }
    }
}
