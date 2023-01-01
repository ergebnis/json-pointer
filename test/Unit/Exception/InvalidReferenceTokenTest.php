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

namespace Ergebnis\Json\Pointer\Test\Unit\Exception;

use Ergebnis\Json\Pointer\Exception;
use Ergebnis\Json\Pointer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\Exception\InvalidReferenceToken
 */
final class InvalidReferenceTokenTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromJsonStringReturnsInvalidReferenceToken(): void
    {
        $value = self::faker()->word();

        $exception = Exception\InvalidReferenceToken::fromJsonString($value);

        $message = \sprintf(
            'Value "%s" does not appear to be a valid JSON Pointer reference token.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testFromIntReturnsInvalidReferenceToken(): void
    {
        $value = self::faker()->numberBetween();

        $exception = Exception\InvalidReferenceToken::fromInt($value);

        $message = \sprintf(
            'Value "%d" does not appear to be a valid JSON Pointer array index.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
