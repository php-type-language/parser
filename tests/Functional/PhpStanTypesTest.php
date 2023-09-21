<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional;

final class PhpStanTypesTest extends TestCase
{
    /**
     * @link https://phpstan.org/writing-php-code/phpdoc-types#basic-types
     *
     * @return iterable<array{string}>
     */
    protected static function basicTypesDataProvider(): iterable
    {
        yield ['int']; yield ['integer'];
        yield ['string'];
        yield ['array-key'];
        yield ['bool']; yield ['boolean'];
        yield ['true'];
        yield ['false'];
        yield ['null'];
        yield ['float'];
        yield ['double'];
        yield ['scalar'];
        yield ['array'];
        yield ['iterable'];
        yield ['callable'];
        yield ['resource'];
        yield ['void'];
        yield ['object'];
    }
}
