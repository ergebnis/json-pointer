<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-pointer
 */

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\DataProvider;
use Ergebnis\Json\Pointer\Exception;
use Ergebnis\Json\Pointer\ReferenceToken;
use Ergebnis\Json\Pointer\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(ReferenceToken::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidReferenceToken::class)]
final class ReferenceTokenTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProvider('provideInvalidJsonStringValue')]
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
    public static function provideInvalidJsonStringValue(): \Generator
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

    #[Framework\Attributes\DataProvider('provideInvalidUriFragmentIdentifierStringValue')]
    public function testFromUriFragmentIdentifierStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidReferenceToken::class);

        ReferenceToken::fromUriFragmentIdentifierString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-6
     *
     * @return \Generator<string, array{0: string}>
     */
    public static function provideInvalidUriFragmentIdentifierStringValue(): \Generator
    {
        $values = [
            'property-with-caret' => 'foo^bar',
            'property-with-hash' => 'foo#bar',
            'property-with-pipe' => 'foo|bar',
            'property-with-quote-double' => 'foo"bar',
            'property-with-slash-backwards' => 'foo\bar',
            'property-with-slash-forwards' => 'foo/bar',
            'property-with-space' => 'foo bar',
            'property-with-unicode-character' => 'foo😆bar',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    #[Framework\Attributes\DataProvider('provideValueJsonStringValueAndUriFragmentIdentifierStringValue')]
    public function testFromJsonStringReturnsReferenceToken(
        string $value,
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
    ): void {
        $referenceToken = ReferenceToken::fromJsonString($jsonStringValue);

        self::assertSame($jsonStringValue, $referenceToken->toJsonString());
        self::assertSame($value, $referenceToken->toString());
        self::assertSame($uriFragmentIdentifierStringValue, $referenceToken->toUriFragmentIdentifierString());
    }

    #[Framework\Attributes\DataProvider('provideValueJsonStringValueAndUriFragmentIdentifierStringValue')]
    public function testFromStringReturnsReferenceToken(
        string $value,
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
    ): void {
        $referenceToken = ReferenceToken::fromString($value);

        self::assertSame($jsonStringValue, $referenceToken->toJsonString());
        self::assertSame($value, $referenceToken->toString());
        self::assertSame($uriFragmentIdentifierStringValue, $referenceToken->toUriFragmentIdentifierString());
    }

    #[Framework\Attributes\DataProvider('provideValueJsonStringValueAndUriFragmentIdentifierStringValue')]
    public function testFromUriFragmentIdentifierStringReturnsReferenceToken(
        string $value,
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
    ): void {
        $referenceToken = ReferenceToken::fromUriFragmentIdentifierString($uriFragmentIdentifierStringValue);

        self::assertSame($jsonStringValue, $referenceToken->toJsonString());
        self::assertSame($value, $referenceToken->toString());
        self::assertSame($uriFragmentIdentifierStringValue, $referenceToken->toUriFragmentIdentifierString());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-6
     *
     * @return \Generator<string, array{0: string, 1: string, 2: string}>
     */
    public static function provideValueJsonStringValueAndUriFragmentIdentifierStringValue(): \Generator
    {
        $values = [
            'array-index-0' => [
                '0',
                '0',
                '0',
            ],
            'array-index-1' => [
                '1',
                '1',
                '1',
            ],
            'array-index-9000' => [
                '9000',
                '9000',
                '9000',
            ],
            'caret' => [
                '^',
                '^',
                '%5E',
            ],
            'empty' => [
                '',
                '',
                '',
            ],
            'percent' => [
                '%',
                '%',
                '%25',
            ],
            'pipe' => [
                '|',
                '|',
                '%7C',
            ],
            'quote-double' => [
                '"',
                '"',
                '%22',
            ],
            'quote-single' => [
                "'",
                "'",
                '%27',
            ],
            'slash-backward' => [
                '\\',
                '\\',
                '%5C',
            ],
            'slash-forward' => [
                '/',
                '~1',
                '~1',
            ],
            'space' => [
                ' ',
                ' ',
                '%20',
            ],
            'word' => [
                'foo',
                'foo',
                'foo',
            ],
            'tilde' => [
                '~',
                '~0',
                '~0',
            ],
            'unicode-character' => [
                '😆',
                '😆',
                '%F0%9F%98%86',
            ],
            'with-caret' => [
                'foo^bar',
                'foo^bar',
                'foo%5Ebar',
            ],
            'with-percent' => [
                'foo%bar',
                'foo%bar',
                'foo%25bar',
            ],
            'with-pipe' => [
                'foo|bar',
                'foo|bar',
                'foo%7Cbar',
            ],
            'with-quote-double' => [
                'foo"bar',
                'foo"bar',
                'foo%22bar',
            ],
            'with-quote-single' => [
                "foo'bar",
                "foo'bar",
                'foo%27bar',
            ],
            'with-slash-backward' => [
                'foo\\bar',
                'foo\\bar',
                'foo%5Cbar',
            ],
            'with-slash-forward' => [
                'foo/bar',
                'foo~1bar',
                'foo~1bar',
            ],
            'with-space' => [
                'foo bar',
                'foo bar',
                'foo%20bar',
            ],
            'with-tilde' => [
                'foo~bar',
                'foo~0bar',
                'foo~0bar',
            ],
            'with-unicode-character' => [
                'foo😆bar',
                'foo😆bar',
                'foo%F0%9F%98%86bar',
            ],
        ];

        foreach ($values as $key => [$value, $jsonStringValue, $uriFragmentIdentifierStringValue]) {
            yield $key => [
                $value,
                $jsonStringValue,
                $uriFragmentIdentifierStringValue,
            ];
        }
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'lessThanZero')]
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidReferenceToken::class);

        ReferenceToken::fromInt($value);
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'greaterThanZero')]
    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'zero')]
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
