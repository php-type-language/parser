<?php

declare(strict_types=1);

namespace Hyper\Bridge;

use Hyper\Bridge\Exception\InstantiatorExceptionInterface;

/**
 * @template TOut of object
 */
interface InstantiatorInterface
{
    /**
     * @param class-string<TOut> $type
     *
     * @return TOut
     * @throws InstantiatorExceptionInterface
     */
    public function new(string $type, array $params = []): object;
}
