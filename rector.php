<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfReturnToEarlyReturnRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $config): void {
    $config->paths([
        __DIR__ . '/libs/hydrator/src',
        __DIR__ . '/libs/parser/src',
        __DIR__ . '/libs/types/src',
        __DIR__ . '/libs/types-dsl/src',
        __DIR__ . '/libs/types-repository/src',
    ]);

    $config->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::EARLY_RETURN,
    ]);

    $config->ruleWithConfiguration(ConsistentPregDelimiterRector::class, [
        ConsistentPregDelimiterRector::DELIMITER => '/',
    ]);

    $config->skip([
        //
        // Do not replace classic properties to promoted eq. These are
        // completely different statements.
        //
        ClassPropertyAssignToConstructorPromotionRector::class,

        //
        // Does not take into account already existing annotations and
        // can replace:
        //  - @var list<string>
        // into:
        //  - @var string[]
        //
        // What is not quite correct behavior.
        VarConstantCommentRector::class,

        //
        // This rector can break the Doctrine that replaces implementations
        // with proxies, like:
        //  - private Collection $relation;          // OK This can be replaced with a proxy
        //  - private readonly Collection $relation; // FAIL
        //
        ReadOnlyPropertyRector::class,

        //
        // Replaces expressions like a "if ($a || $b)" into 2 separate
        // expressions, which may not always be convenient and beautiful :3
        //
        ChangeOrIfReturnToEarlyReturnRector::class,
    ]);
};
