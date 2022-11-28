<?php

declare(strict_types=1);

namespace Hyper\Pool\Exception;

class PoolOverflowException extends \OverflowException implements PoolExceptionInterface
{
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    final public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param positive-int $max
     *
     * @return static
     */
    public static function fromMaxEntries(int $max): self
    {
        $message = 'Can not create entry: The maximum (%s) allowed number of free entries is used';

        return new static(\sprintf($message, $max));
    }
}
