<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-pointer
 */

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\Json\Pointer\Exception;
use Ergebnis\Json\Pointer\ReferenceToken;
use Ergebnis\Json\Pointer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\ReferenceToken
 *
 * @uses \Ergebnis\Json\Pointer\Exception\InvalidReferenceToken
 */
final class ReferenceTokenTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideInvalidEscapedStringValue
     */
    public function testFromEscapedJsonStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidReferenceToken::class);

        ReferenceToken::fromJsonString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideInvalidEscapedStringValue(): \Generator
    {
        $values = [
            'property-with-unescaped-forward-slash' => 'foo/bar',
            'property-with-unescaped-tilde' => 'foo~bar',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValueAndJsonStringValue
     */
    public function testFromJsonStringReturnsReferenceToken(
        string $value,
        string $jsonStringValue
    ): void {
        $referenceToken = ReferenceToken::fromJsonString($jsonStringValue);

        self::assertSame($jsonStringValue, $referenceToken->toJsonString());
        self::assertSame($value, $referenceToken->toString());
    }

    /**
     * @dataProvider provideValueAndJsonStringValue
     */
    public function testFromStringReturnsReferenceToken(
        string $value,
        string $jsonStringValue
    ): void {
        $referenceToken = ReferenceToken::fromString($value);

        self::assertSame($jsonStringValue, $referenceToken->toJsonString());
        self::assertSame($value, $referenceToken->toString());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public function provideValueAndJsonStringValue(): \Generator
    {
        $values = [
            'integerish-9000' => [
                '9000',
                '9000',
            ],
            'integerish-zero' => [
                '0',
                '0',
            ],
            'string-back-slash' => [
                '\\',
                '\\',
            ],
            'string-caret' => [
                '^',
                '^',
            ],
            'string-percent' => [
                '%',
                '%',
            ],
            'string-pipe' => [
                '|',
                '|',
            ],
            'string-quote-double' => [
                '"',
                '"',
            ],
            'string-quote-single' => [
                "'",
                "'",
            ],
            'string-slash-backward' => [
                '\\',
                '\\',
            ],
            'string-slash-forward' => [
                '/',
                '~1',
            ],
            'string-space' => [
                ' ',
                ' ',
            ],
            'string-word' => [
                'foo',
                'foo',
            ],
            'string-tilde' => [
                '~',
                '~0',
            ],
            'string-unicode-character' => [
                '😆',
                '😆',
            ],
            'string-with-caret' => [
                'foo^bar',
                'foo^bar',
            ],
            'string-with-percent' => [
                'foo%bar',
                'foo%bar',
            ],
            'string-with-pipe' => [
                'foo|bar',
                'foo|bar',
            ],
            'string-with-quote-double' => [
                'foo"bar',
                'foo"bar',
            ],
            'string-with-quote-single' => [
                "foo'bar",
                "foo'bar",
            ],
            'string-with-slash-backward' => [
                'foo\\bar',
                'foo\\bar',
            ],
            'string-with-slash-forward' => [
                'foo/bar',
                'foo~1bar',
            ],
            'string-with-space' => [
                'foo bar',
                'foo bar',
            ],
            'string-with-tilde' => [
                'foo~bar',
                'foo~0bar',
            ],
            'string-with-unicode-character' => [
                'foo😆bar',
                'foo😆bar',
            ],
        ];

        foreach ($values as $key => [$unescaped, $escaped]) {
            yield $key => [
                $unescaped,
                $escaped,
            ];
        }
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::lessThanZero()
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidReferenceToken::class);

        ReferenceToken::fromInt($value);
    }

    /**
     * @dataProvider \Ergebnis\DataProvider\IntProvider::greaterThanZero()
     * @dataProvider \Ergebnis\DataProvider\IntProvider::zero()
     */
    public function testFromIntReturnsReferenceToken(int $value): void
    {
        $referenceToken = ReferenceToken::fromInt($value);

        self::assertSame((string) $value, $referenceToken->toJsonString());
        self::assertSame((string) $value, $referenceToken->toString());
    }

    public function testEqualsReturnsFalseWhenJsonStringValueIsDifferent(): void
    {
        $faker = self::faker();

        $one = ReferenceToken::fromString($faker->sentence());
        $two = ReferenceToken::fromString($faker->sentence());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenJsonStringValueIsSame(): void
    {
        $value = self::faker()->sentence();

        $one = ReferenceToken::fromString($value);
        $two = ReferenceToken::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
