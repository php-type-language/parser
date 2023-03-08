<?php

declare(strict_types=1);

namespace Hyper\Bridge;

/**
 * @template TOutLiteral
 * @template TType of object
 * @template TLogicalOutput of object<TType>
 * @template TMonadOutput of object<TType>
 *
 * @template-extends LiteralsProviderInterface<TOutLiteral>
 * @template-extends LogicalProviderInterface<TType, TLogicalOutput>
 * @template-extends MonadProviderInterface<TType, TMonadOutput>
 * @template-extends InstantiatorInterface<TType>
 */
interface FactoryInterface extends
    LiteralsProviderInterface,
    LogicalProviderInterface,
    MonadProviderInterface,
    InstantiatorInterface
{
}
