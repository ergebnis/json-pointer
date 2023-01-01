<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-pointer
 */

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\Json\Pointer\Exception;
use Ergebnis\Json\Pointer\JsonPointer;
use Ergebnis\Json\Pointer\ReferenceToken;
use PHPUnit\Framework;

/**
 * @internal
 *
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
        $this->expectException(Exception\InvalidJsonPointer::class);

        JsonPointer::fromJsonString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideInvalidJsonStringValue(): \Generator
    {
        $values = [
            'does-not-start-with-forward-slash' => 'foo',
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
     * @param array<int, ReferenceToken> $referenceTokens
     */
    public function testFromJsonStringReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens,
    ): void {
        $jsonPointer = JsonPointer::fromJsonString($jsonStringValue);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @dataProvider provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens
     *
     * @param array<int, ReferenceToken> $referenceTokens
     */
    public function testFromUriFragmentIdentifierStringReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens,
    ): void {
        $jsonPointer = JsonPointer::fromUriFragmentIdentifierString($uriFragmentIdentifierStringValue);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @dataProvider provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens
     *
     * @param array<int, ReferenceToken> $referenceTokens
     */
    public function testFromReferenceTokensReturnsJsonPointer(
        string $jsonStringValue,
        string $uriFragmentIdentifierStringValue,
        array $referenceTokens,
    ): void {
        $jsonPointer = JsonPointer::fromReferenceTokens(...$referenceTokens);

        self::assertSame($jsonStringValue, $jsonPointer->toJsonString());
        self::assertSame($uriFragmentIdentifierStringValue, $jsonPointer->toUriFragmentIdentifierString());
        self::assertEquals($referenceTokens, $jsonPointer->toReferenceTokens());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string, 1: string, 2: array<int, ReferenceToken>}>
     */
    public function provideJsonStringValueUriFragmentIdentifierStringValueAndReferenceTokens(): \Generator
    {
        $values = [
            'array-index-0' => [
                '/0',
                '#/0',
                [
                    ReferenceToken::fromInt(0),
                ],
            ],
            'array-index-1' => [
                '/1',
                '#/1',
                [
                    ReferenceToken::fromInt(1),
                ],
            ],
            'array-index-9000' => [
                '/9000',
                '#/9000',
                [
                    ReferenceToken::fromInt(9000),
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
                    ReferenceToken::fromString('^'),
                ],
            ],
            'property-empty' => [
                '/',
                '#/',
                [
                    ReferenceToken::fromString(''),
                ],
            ],
            'property-percent' => [
                '/%',
                '#/%25',
                [
                    ReferenceToken::fromString('%'),
                ],
            ],
            'property-pipe' => [
                '/|',
                '#/%7C',
                [
                    ReferenceToken::fromString('|'),
                ],
            ],
            'property-quote-double' => [
                '/"',
                '#/%22',
                [
                    ReferenceToken::fromString('"'),
                ],
            ],
            'property-quote-single' => [
                "/'",
                '#/%27',
                [
                    ReferenceToken::fromString("'"),
                ],
            ],
            'property-slash-backward' => [
                '/\\',
                '#/%5C',
                [
                    ReferenceToken::fromString('\\'),
                ],
            ],
            'property-slash-forward-escaped' => [
                '/~1',
                '#/~1',
                [
                    ReferenceToken::fromString('/'),
                ],
            ],
            'property-space' => [
                '/ ',
                '#/%20',
                [
                    ReferenceToken::fromString(' '),
                ],
            ],
            'property-tilde-escaped' => [
                '/~0',
                '#/~0',
                [
                    ReferenceToken::fromString('~'),
                ],
            ],
            'property-unicode-character' => [
                '/😆',
                '#/%F0%9F%98%86',
                [
                    ReferenceToken::fromString('😆'),
                ],
            ],
            'property-with-array-index' => [
                '/foo/0',
                '#/foo/0',
                [
                    ReferenceToken::fromString('foo'),
                    ReferenceToken::fromInt(0),
                ],
            ],
            'property-with-caret' => [
                '/foo^bar',
                '#/foo%5Ebar',
                [
                    ReferenceToken::fromString('foo^bar'),
                ],
            ],
            'property-with-percent' => [
                '/foo%bar',
                '#/foo%25bar',
                [
                    ReferenceToken::fromString('foo%bar'),
                ],
            ],
            'property-with-pipe' => [
                '/foo|bar',
                '#/foo%7Cbar',
                [
                    ReferenceToken::fromString('foo|bar'),
                ],
            ],
            'property-with-quote-double' => [
                '/foo"bar',
                '#/foo%22bar',
                [
                    ReferenceToken::fromString('foo"bar'),
                ],
            ],
            'property-with-quote-single' => [
                "/foo'bar",
                '#/foo%27bar',
                [
                    ReferenceToken::fromString("foo'bar"),
                ],
            ],
            'property-with-slash-backward' => [
                '/foo\\bar',
                '#/foo%5Cbar',
                [
                    ReferenceToken::fromString('foo\\bar'),
                ],
            ],
            'property-with-slash-forward-escaped' => [
                '/foo~1bar',
                '#/foo~1bar',
                [
                    ReferenceToken::fromString('foo/bar'),
                ],
            ],
            'property-with-tilde-escaped' => [
                '/foo~0bar',
                '#/foo~0bar',
                [
                    ReferenceToken::fromString('foo~bar'),
                ],
            ],
            'property-with-unicode-character' => [
                '/foo😆bar',
                '#/foo%F0%9F%98%86bar',
                [
                    ReferenceToken::fromString('foo😆bar'),
                ],
            ],
            'property-with-word' => [
                '/foo/bar',
                '#/foo/bar',
                [
                    ReferenceToken::fromString('foo'),
                    ReferenceToken::fromString('bar'),
                ],
            ],
            'property-word' => [
                '/foo',
                '#/foo',
                [
                    ReferenceToken::fromString('foo'),
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
        $this->expectException(Exception\InvalidJsonPointer::class);

        JsonPointer::fromUriFragmentIdentifierString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideInvalidUriFragmentIdentifierStringValue(): \Generator
    {
        $values = [
            'does-not-start-with-hash' => 'foo',
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
        $jsonPointer = JsonPointer::document();

        self::assertSame('', $jsonPointer->toJsonString());
        self::assertSame('#', $jsonPointer->toUriFragmentIdentifierString());
    }

    /**
     * @dataProvider provideJsonPointerReferenceTokenAndExpectedJsonPointer
     */
    public function testAppendReturnsJsonPointer(
        JsonPointer $jsonPointer,
        ReferenceToken $referenceToken,
        JsonPointer $expectedJsonPointer,
    ): void {
        $mutated = $jsonPointer->append($referenceToken);

        self::assertNotSame($jsonPointer, $mutated);
        self::assertEquals($expectedJsonPointer, $mutated);
    }

    /**
     * @return \Generator<string, array{0: JsonPointer, 1: ReferenceToken, 2: JsonPointer}>
     */
    public function provideJsonPointerReferenceTokenAndExpectedJsonPointer(): \Generator
    {
        $values = [
            'document-and-reference-token-from-unescaped-string' => [
                JsonPointer::document(),
                ReferenceToken::fromString('foo'),
                JsonPointer::fromJsonString('/foo'),
            ],
            'document-from-string-and-reference-token-from-unescaped-string' => [
                JsonPointer::fromJsonString(''),
                ReferenceToken::fromString('foo'),
                JsonPointer::fromJsonString('/foo'),
            ],
            'pointer-and-reference-token-from-int' => [
                JsonPointer::fromJsonString('/foo'),
                ReferenceToken::fromInt(9000),
                JsonPointer::fromJsonString('/foo/9000'),
            ],
            'pointer-and-reference-token-from-unescaped-string' => [
                JsonPointer::fromJsonString('/foo'),
                ReferenceToken::fromString('bar/baz'),
                JsonPointer::fromJsonString('/foo/bar~1baz'),
            ],
            'pointer-and-reference-token-from-escaped-string' => [
                JsonPointer::fromJsonString('/foo'),
                ReferenceToken::fromJsonString('bar~1baz'),
                JsonPointer::fromJsonString('/foo/bar~1baz'),
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
        $one = JsonPointer::fromJsonString('/foo/bar/0/baz~0');
        $two = JsonPointer::fromJsonString('/foo/bar/1');

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = '/foo/bar/0/baz~0';

        $one = JsonPointer::fromJsonString($value);
        $two = JsonPointer::fromJsonString($value);

        self::assertTrue($one->equals($two));
    }
}
