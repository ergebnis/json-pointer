<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-pointer
 */

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Pointer\JsonPointer
 *
 * @uses \Ergebnis\Json\Pointer\Exception\InvalidJsonPointer
 * @uses \Ergebnis\Json\Pointer\ReferenceToken
 */
final class JsonPointerTest extends Framework\TestCase
{
    /**
     * @dataProvider provideInvalidJsonStringValue
     */
    public function testFromJsonStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Pointer\Exception\InvalidJsonPointer::class);

        Pointer\JsonPointer::fromJsonString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public static function provideInvalidJsonStringValue(): iterable
    {
        $values = [
            'does-not-start-with-forward-slash' => 'foo',
            'is-new-line' => "\n",
            'property-with-tilde-followed-by-word' => '/foo~bar',
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
                '/foo~%d',
                $digit,
            );

            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens
     *
     * @param array<int, Pointer\ReferenceToken> $referenceTokens
     */
    public function testFromJsonStringReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens
    ): void {
        $jsonPointer = Pointer\JsonPointer::fromJsonString($jsonStringValue);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @dataProvider provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens
     *
     * @param array<int, Pointer\ReferenceToken> $referenceTokens
     */
    public function testFromUriFragmentIdentifierStringReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens
    ): void {
        $jsonPointer = Pointer\JsonPointer::fromUriFragmentIdentifierString($uriFragmentIdentifierStringValue);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @dataProvider provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens
     *
     * @param array<int, Pointer\ReferenceToken> $referenceTokens
     */
    public function testFromReferenceTokensReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens
    ): void {
        $jsonPointer = Pointer\JsonPointer::fromReferenceTokens(...$referenceTokens);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string, 1: string, 2: array<int, Pointer\ReferenceToken>}>
     */
    public static function provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens(): iterable
    {
        $values = [
            'array-index-0' => [
                '/0',
                '#/0',
                [
                    Pointer\ReferenceToken::fromInt(0),
                ],
            ],
            'array-index-1' => [
                '/1',
                '#/1',
                [
                    Pointer\ReferenceToken::fromInt(1),
                ],
            ],
            'array-index-9000' => [
                '/9000',
                '#/9000',
                [
                    Pointer\ReferenceToken::fromInt(9000),
                ],
            ],
            'document' => [
                '',
                '#',
                [],
            ],
            'property-caret' => [
                '/^',
                '#/%5E',
                [
                    Pointer\ReferenceToken::fromString('^'),
                ],
            ],
            'property-empty' => [
                '/',
                '#/',
                [
                    Pointer\ReferenceToken::fromString(''),
                ],
            ],
            'property-percent' => [
                '/%',
                '#/%25',
                [
                    Pointer\ReferenceToken::fromString('%'),
                ],
            ],
            'property-pipe' => [
                '/|',
                '#/%7C',
                [
                    Pointer\ReferenceToken::fromString('|'),
                ],
            ],
            'property-quote-double' => [
                '/"',
                '#/%22',
                [
                    Pointer\ReferenceToken::fromString('"'),
                ],
            ],
            'property-quote-single' => [
                "/'",
                '#/%27',
                [
                    Pointer\ReferenceToken::fromString("'"),
                ],
            ],
            'property-slash-backward' => [
                '/\\',
                '#/%5C',
                [
                    Pointer\ReferenceToken::fromString('\\'),
                ],
            ],
            'property-slash-forward-escaped' => [
                '/~1',
                '#/~1',
                [
                    Pointer\ReferenceToken::fromString('/'),
                ],
            ],
            'property-space' => [
                '/ ',
                '#/%20',
                [
                    Pointer\ReferenceToken::fromString(' '),
                ],
            ],
            'property-tilde-escaped' => [
                '/~0',
                '#/~0',
                [
                    Pointer\ReferenceToken::fromString('~'),
                ],
            ],
            'property-unicode-character' => [
                '/😆',
                '#/%F0%9F%98%86',
                [
                    Pointer\ReferenceToken::fromString('😆'),
                ],
            ],
            'property-with-array-index' => [
                '/foo/0',
                '#/foo/0',
                [
                    Pointer\ReferenceToken::fromString('foo'),
                    Pointer\ReferenceToken::fromInt(0),
                ],
            ],
            'property-with-caret' => [
                '/foo^bar',
                '#/foo%5Ebar',
                [
                    Pointer\ReferenceToken::fromString('foo^bar'),
                ],
            ],
            'property-with-percent' => [
                '/foo%bar',
                '#/foo%25bar',
                [
                    Pointer\ReferenceToken::fromString('foo%bar'),
                ],
            ],
            'property-with-pipe' => [
                '/foo|bar',
                '#/foo%7Cbar',
                [
                    Pointer\ReferenceToken::fromString('foo|bar'),
                ],
            ],
            'property-with-quote-double' => [
                '/foo"bar',
                '#/foo%22bar',
                [
                    Pointer\ReferenceToken::fromString('foo"bar'),
                ],
            ],
            'property-with-quote-single' => [
                "/foo'bar",
                '#/foo%27bar',
                [
                    Pointer\ReferenceToken::fromString("foo'bar"),
                ],
            ],
            'property-with-slash-backward' => [
                '/foo\\bar',
                '#/foo%5Cbar',
                [
                    Pointer\ReferenceToken::fromString('foo\\bar'),
                ],
            ],
            'property-with-slash-forward-escaped' => [
                '/foo~1bar',
                '#/foo~1bar',
                [
                    Pointer\ReferenceToken::fromString('foo/bar'),
                ],
            ],
            'property-with-tilde-escaped' => [
                '/foo~0bar',
                '#/foo~0bar',
                [
                    Pointer\ReferenceToken::fromString('foo~bar'),
                ],
            ],
            'property-with-unicode-character' => [
                '/foo😆bar',
                '#/foo%F0%9F%98%86bar',
                [
                    Pointer\ReferenceToken::fromString('foo😆bar'),
                ],
            ],
            'property-with-word' => [
                '/foo/bar',
                '#/foo/bar',
                [
                    Pointer\ReferenceToken::fromString('foo'),
                    Pointer\ReferenceToken::fromString('bar'),
                ],
            ],
            'property-word' => [
                '/foo',
                '#/foo',
                [
                    Pointer\ReferenceToken::fromString('foo'),
                ],
            ],
        ];

        foreach ($values as $key => [$jsonStringValue, $uriFragmentIdentifierStringValue,$referenceTokens]) {
            yield $key => [
                $jsonStringValue,
                $uriFragmentIdentifierStringValue,
                $referenceTokens,
            ];
        }
    }

    /**
     * @dataProvider provideInvalidUriFragmentIdentifierStringValue
     */
    public function testFromUriFragmentIdentifierStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Pointer\Exception\InvalidJsonPointer::class);

        Pointer\JsonPointer::fromUriFragmentIdentifierString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public static function provideInvalidUriFragmentIdentifierStringValue(): iterable
    {
        $values = [
            'does-not-start-with-hash' => 'foo',
            'hash-followed-by-new-line' => "#\n",
            'property-followed-by-new-line' => "#/foo\n",
            'property-with-tilde-followed-by-word' => '#foo~bar',
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
                '#foo~%d',
                $digit,
            );

            yield $key => [
                $value,
            ];
        }
    }

    public function testDocumentReturnsJsonPointer(): void
    {
        $jsonPointer = Pointer\JsonPointer::document();

        self::assertSame('', $jsonPointer->toJsonString());
        self::assertSame('#', $jsonPointer->toUriFragmentIdentifierString());
    }

    /**
     * @dataProvider provideJsonPointerReferenceTokenAndExpectedJsonPointer
     */
    public function testAppendReturnsJsonPointer(
        Pointer\JsonPointer $jsonPointer,
        Pointer\ReferenceToken $referenceToken,
        Pointer\JsonPointer $expectedJsonPointer
    ): void {
        $mutated = $jsonPointer->append($referenceToken);

        self::assertNotSame($jsonPointer, $mutated);
        self::assertEquals($expectedJsonPointer, $mutated);
    }

    /**
     * @return \Generator<string, array{0: Pointer\JsonPointer, 1: Pointer\ReferenceToken, 2: Pointer\JsonPointer}>
     */
    public static function provideJsonPointerReferenceTokenAndExpectedJsonPointer(): iterable
    {
        $values = [
            'document-and-reference-token-from-unescaped-string' => [
                Pointer\JsonPointer::document(),
                Pointer\ReferenceToken::fromString('foo'),
                Pointer\JsonPointer::fromJsonString('/foo'),
            ],
            'document-from-string-and-reference-token-from-unescaped-string' => [
                Pointer\JsonPointer::fromJsonString(''),
                Pointer\ReferenceToken::fromString('foo'),
                Pointer\JsonPointer::fromJsonString('/foo'),
            ],
            'pointer-and-reference-token-from-int' => [
                Pointer\JsonPointer::fromJsonString('/foo'),
                Pointer\ReferenceToken::fromInt(9000),
                Pointer\JsonPointer::fromJsonString('/foo/9000'),
            ],
            'pointer-and-reference-token-from-unescaped-string' => [
                Pointer\JsonPointer::fromJsonString('/foo'),
                Pointer\ReferenceToken::fromString('bar/baz'),
                Pointer\JsonPointer::fromJsonString('/foo/bar~1baz'),
            ],
            'pointer-and-reference-token-from-escaped-string' => [
                Pointer\JsonPointer::fromJsonString('/foo'),
                Pointer\ReferenceToken::fromJsonString('bar~1baz'),
                Pointer\JsonPointer::fromJsonString('/foo/bar~1baz'),
            ],
        ];

        foreach ($values as $key => [$expectedJsonPointer, $jsonPointer, $segment]) {
            yield $key => [
                $expectedJsonPointer,
                $jsonPointer,
                $segment,
            ];
        }
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Pointer\JsonPointer::fromJsonString('/foo/bar/0/baz~0');
        $two = Pointer\JsonPointer::fromJsonString('/foo/bar/1');

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = '/foo/bar/0/baz~0';

        $one = Pointer\JsonPointer::fromJsonString($value);
        $two = Pointer\JsonPointer::fromJsonString($value);

        self::assertTrue($one->equals($two));
    }
}
