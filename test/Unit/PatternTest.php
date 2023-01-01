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

use Ergebnis\Json\Pointer\Pattern;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\Pattern
 */
final class PatternTest extends Framework\TestCase
{
    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    public function testJsonStringJsonPointerEqualsPattern(): void
    {
        $jsonStringReferenceToken = self::jsonStringReferenceToken();

        $expected = "/^(?P<jsonStringJsonPointer>(\\/{$jsonStringReferenceToken})*)$/u";

        self::assertSame($expected, Pattern::JSON_STRING_JSON_POINTER);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    public function testJsonStringReferenceTokenEqualsPattern(): void
    {
        $jsonStringReferenceToken = self::jsonStringReferenceToken();

        $expected = "/^{$jsonStringReferenceToken}$/u";

        self::assertSame($expected, Pattern::JSON_STRING_REFERENCE_TOKEN);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-6
     * @see https://datatracker.ietf.org/doc/html/rfc3986#appendix-A
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.3
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.1
     */
    public function testUriFragmentIdentifierJsonPointerEqualsPattern(): void
    {
        $uriFragmentIdentifierReferenceToken = self::uriFragmentIdentifierReferenceToken();

        $expected = "/^(?P<uriFragmentIdentifierJsonPointer>#(\\/{$uriFragmentIdentifierReferenceToken})*)$/u";

        self::assertSame($expected, Pattern::URI_FRAGMENT_IDENTIFIER_JSON_POINTER);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-6
     * @see https://datatracker.ietf.org/doc/html/rfc3986#appendix-A
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.3
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.1
     */
    public function testUriFragmentIdentifierReferenceTokenEqualsPattern(): void
    {
        $uriFragmentIdentifierReferenceToken = self::uriFragmentIdentifierReferenceToken();

        $expected = "/^{$uriFragmentIdentifierReferenceToken}$/u";

        self::assertSame($expected, Pattern::URI_FRAGMENT_IDENTIFIER_REFERENCE_TOKEN);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    private static function jsonStringReferenceToken(): string
    {
        $unescaped = '(?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])';
        $escaped = '(?P<escaped>~[01])';

        return "(?P<referenceToken>({$unescaped}|{$escaped})*)";
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-6
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-3.5
     * @see https://datatracker.ietf.org/doc/html/rfc3986#appendix-A
     */
    private static function uriFragmentIdentifierReferenceToken(): string
    {
        $alpha = '(?P<alpha>[a-zA-Z])';
        $digit = '(?P<digit>\d)';
        $unreserved = "(?P<unreserved>({$alpha}|{$digit}|-|\\.|_|~))";
        $hexDig = '(?P<hexDig>[0-9a-fA-F])';
        $pctEncoded = "(?P<pctEncoded>%{$hexDig}{2})";
        $subDelims = '(?P<subDelims>(!|\$|&|\'|\(|\)|\*|\+|,|;|=))';

        $pchar = "(?P<pchar>({$unreserved}|{$pctEncoded}|{$subDelims}|:|@))";

        return "(?P<referenceToken>({$pchar}*))";
    }
}
