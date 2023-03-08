<?php

declare(strict_types=1);

use Hyper\Type;

return [
    'array-key' => Type\ArrayKey::getInstance(),
    'array' => Type\ArrayType::getInstance(),
    'bool' => Type\BoolType::getInstance(),
    'true' => Type\Literal\TrueLiteral::getInstance(),
    'false' => Type\Literal\FalseLiteral::getInstance(),
    'null' => Type\Literal\NullLiteral::getInstance(),
    'float' => Type\FloatType::getInstance(),
    'int' => Type\IntType::getInstance(),
    'scalar' => Type\ScalarType::getInstance(),
    'max' => Type\Marker\MaxMarker::getInstance(),
    'min' => Type\Marker\MinMarker::getInstance(),
    'positive-int' => new Type\IntType(min: 1),
    'negative-int' => new Type\IntType(max: -1),
    'non-positive-int' => new Type\IntType(max: 0),
    'non-negative-int' => new Type\IntType(min: 0),
    'mixed' => Type\MixedType::getInstance(),
    'string' => Type\StringType::getInstance(),
    'object' => Type\ObjectType::getInstance(),
];
