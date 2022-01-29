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

use Ergebnis\Json\Pointer\Pattern;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\Pattern
 */
final class PatternTest extends Framework\TestCase
{
    public function testJsonPointerJsonStringEqualsPattern(): void
    {
        $referenceToken = self::referenceToken();

        $expected = "/^(?P<jsonPointer>(\\/{$referenceToken})*)$/u";

        self::assertSame($expected, Pattern::JSON_POINTER_JSON_STRING);
    }

    public function testReferenceTokenEqualsPattern(): void
    {
        $referenceToken = self::referenceToken();

        $expected = "/^{$referenceToken}$/u";

        self::assertSame($expected, Pattern::REFERENCE_TOKEN);
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    private static function referenceToken(): string
    {
        $unescaped = '(?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])';
        $escaped = '(?P<escaped>~[01])';

        return "(?P<referenceToken>({$unescaped}|{$escaped})*)";
    }
}
