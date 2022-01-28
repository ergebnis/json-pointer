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
     * @dataProvider provideInvalidValue
     */
    public function testFromStringRejectsInvalidValue(string $value): void
    {
        $this->expectException(Exception\InvalidJsonPointer::class);

        JsonPointer::fromString($value);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideInvalidValue(): \Generator
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
     * @dataProvider provideValidValue
     */
    public function testFromStringReturnsJsonPointer(string $value): void
    {
        $jsonPointer = JsonPointer::fromString($value);

        self::assertSame($value, $jsonPointer->toString());
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @return \Generator<string, array{0: string}>
     */
    public function provideValidValue(): \Generator
    {
        $values = [
            'document' => '',
            'document-root' => '/',
            'property-points-to-array-element' => '/foo/0',
            'property-with-escaped-forward-slash' => '/a~1b',
            'property-with-escaped-tilde' => '/m~0n',
            'property-with-unescaped-back-slash' => '/i\\j',
            'property-with-unescaped-caret' => '/e^f',
            'property-with-unescaped-double-quote' => '/k"l',
            'property-with-unescaped-percent' => '/c%d',
            'property-pipe' => '/|',
            'property-with-pipe' => '/foo|bar',
            'property-quote-single' => "/foo'bar",
            'property-quote-double' => '/foo"bar',
            'property-space' => '/ ',
            'property-text' => '/foo',
            'property-unicode-character' => '/😆',
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

        self::assertSame('', $jsonPointer->toString());
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
                ReferenceToken::fromUnescapedString('foo'),
                JsonPointer::fromString('/foo'),
            ],
            'document-from-string-and-reference-token-from-unescaped-string' => [
                JsonPointer::fromString(''),
                ReferenceToken::fromUnescapedString('foo'),
                JsonPointer::fromString('/foo'),
            ],
            'pointer-and-reference-token-from-int' => [
                JsonPointer::fromString('/foo'),
                ReferenceToken::fromInt(9000),
                JsonPointer::fromString('/foo/9000'),
            ],
            'pointer-and-reference-token-from-unescaped-string' => [
                JsonPointer::fromString('/foo'),
                ReferenceToken::fromUnescapedString('bar/baz'),
                JsonPointer::fromString('/foo/bar~1baz'),
            ],
            'pointer-and-reference-token-from-escaped-string' => [
                JsonPointer::fromString('/foo'),
                ReferenceToken::fromEscapedString('bar~1baz'),
                JsonPointer::fromString('/foo/bar~1baz'),
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
        $one = JsonPointer::fromString('/foo/bar/0/baz~0');
        $two = JsonPointer::fromString('/foo/bar/1');

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsFalseWhenValueIsSame(): void
    {
        $value = '/foo/bar/0/baz~0';

        $one = JsonPointer::fromString($value);
        $two = JsonPointer::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
