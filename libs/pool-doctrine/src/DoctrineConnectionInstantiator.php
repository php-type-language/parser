<?php

declare(strict_types=1);

namespace Hyper\Pool\Doctrine;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Hyper\Pool\Instantiator\InstantiatorInterface;

/**
 * @template-implements InstantiatorInterface<Connection>
 */
final class DoctrineConnectionInstantiator implements InstantiatorInterface
{
    /**
     * @param Driver $driver
     * @param array $params
     */
    public function __construct(
        private readonly Driver $driver,
        private readonly array $params,
    ) {
    }

    /**
     * @param object|null $previous
     *
     * @return object
     * @throws Driver\Exception
     */
    public function create(?object $previous): object
    {
        \assert($previous === null || $previous instanceof Connection);

        if ($previous === null) {
            return $this->driver->connect($this->params);
        }

        $this->reset($previous);

        return $previous;
    }

    /**
     * @param Connection $connection
     *
     * @return void
     * @throws Exception
     */
    private function reset(Connection $connection): void
    {
        do {
            try {
                $connection->rollBack();
            } catch (\Throwable) {
                return;
            }
        } while (true);
    }
}
