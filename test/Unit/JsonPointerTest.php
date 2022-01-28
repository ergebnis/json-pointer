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
use Ergebnis\Json\Pointer\JsonPointer;
use Ergebnis\Json\Pointer\ReferenceToken;
use Ergebnis\Json\Pointer\Test;
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
    use Test\Util\Helper;

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
            'property-with-unescaped-tilde' => '/foo~bar',
            'property-with-unescaped-tildes' => '/foo~~bar',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValidJsonStringValue
     */
    public function testFromJsonStringReturnsJsonPointer(string $value): void
    {
        $jsonPointer = JsonPointer::fromJsonString($value);

        self::assertSame($value, $jsonPointer->toJsonString());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideValidJsonStringValue(): \Generator
    {
        $values = [
            'array-index-0' => '/0',
            'array-index-1' => '/1',
            'array-index-9000' => '/9000',
            'document' => '',
            'document-root' => '/',
            'property-caret' => '/^',
            'property-percent' => '/%',
            'property-pipe' => '/|',
            'property-quote-double' => '/"',
            'property-quote-single' => "/'",
            'property-slash-backward' => '/\\',
            'property-slash-forward-escaped' => '/~1',
            'property-space' => '/ ',
            'property-tilde-escaped' => '/~0',
            'property-unicode-character' => '/😆',
            'property-with-array-index' => '/foo/0',
            'property-with-caret' => '/foo^bar',
            'property-with-percent' => '/foo%bar',
            'property-with-pipe' => '/foo|bar',
            'property-with-quote-double' => '/foo"bar',
            'property-with-quote-single' => "/foo'bar",
            'property-with-slash-backward' => '/foo\\bar',
            'property-with-slash-forward-escaped' => '/foo~1bar',
            'property-with-tilde-escaped' => '/foo~0bar',
            'property-with-unicode-character' => '/foo😆bar',
            'property-with-word' => '/foo/bar',
            'property-word' => '/foo',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testDocumentReturnsJsonPointer(): void
    {
        $jsonPointer = JsonPointer::document();

        self::assertSame('', $jsonPointer->toJsonString());
    }

    /**
     * @dataProvider provideJsonPointerReferenceTokenAndExpectedJsonPointer
     */
    public function testAppendReturnsJsonPointer(
        JsonPointer $jsonPointer,
        ReferenceToken $referenceToken,
        JsonPointer $expectedJsonPointer
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

    public function testEqualsReturnsFalseWhenValueIsSame(): void
    {
        $value = '/foo/bar/0/baz~0';

        $one = JsonPointer::fromJsonString($value);
        $two = JsonPointer::fromJsonString($value);

        self::assertTrue($one->equals($two));
    }
}
