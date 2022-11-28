<?php

declare(strict_types=1);

namespace Hyper\Pool;

enum Status
{
    /**
     * The status of the active (used) object in the pool.
     */
    case ACTIVE;

    /**
     * The status of a free object in the pool that can be
     * reused in the future.
     */
    case FREE;
}
