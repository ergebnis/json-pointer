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
     * @dataProvider provideInvalidJsonStringValue
     */
    public function testFromJsonStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidReferenceToken::class);

        ReferenceToken::fromJsonString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideInvalidJsonStringValue(): \Generator
    {
        $values = [
            'property-with-slash-forward' => 'foo/bar',
            'property-with-tilde-followed-by-word' => 'foo~bar',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }

        foreach (\range(2, 9) as $digit) {
            $key = \sprintf(
                'property-with-tilde-followed-by-digit-%d',
                $digit,
            );

            $value = \sprintf(
                'foo~%d',
                $digit,
            );

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
            'array-index-0' => [
                '0',
                '0',
            ],
            'array-index-1' => [
                '1',
                '1',
            ],
            'array-index-9000' => [
                '9000',
                '9000',
            ],
            'caret' => [
                '^',
                '^',
            ],
            'percent' => [
                '%',
                '%',
            ],
            'pipe' => [
                '|',
                '|',
            ],
            'quote-double' => [
                '"',
                '"',
            ],
            'quote-single' => [
                "'",
                "'",
            ],
            'slash-backward' => [
                '\\',
                '\\',
            ],
            'slash-forward' => [
                '/',
                '~1',
            ],
            'space' => [
                ' ',
                ' ',
            ],
            'word' => [
                'foo',
                'foo',
            ],
            'tilde' => [
                '~',
                '~0',
            ],
            'unicode-character' => [
                '😆',
                '😆',
            ],
            'with-caret' => [
                'foo^bar',
                'foo^bar',
            ],
            'with-percent' => [
                'foo%bar',
                'foo%bar',
            ],
            'with-pipe' => [
                'foo|bar',
                'foo|bar',
            ],
            'with-quote-double' => [
                'foo"bar',
                'foo"bar',
            ],
            'with-quote-single' => [
                "foo'bar",
                "foo'bar",
            ],
            'with-slash-backward' => [
                'foo\\bar',
                'foo\\bar',
            ],
            'with-slash-forward' => [
                'foo/bar',
                'foo~1bar',
            ],
            'with-space' => [
                'foo bar',
                'foo bar',
            ],
            'with-tilde' => [
                'foo~bar',
                'foo~0bar',
            ],
            'with-unicode-character' => [
                'foo😆bar',
                'foo😆bar',
            ],
        ];

        foreach ($values as $key => [$value, $jsonStringValue]) {
            yield $key => [
                $value,
                $jsonStringValue,
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
