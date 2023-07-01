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

#[Framework\Attributes\CoversClass(Exception\InvalidJsonPointer::class)]
final class InvalidJsonPointerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromJsonStringReturnsInvalidJsonPointerException(): void
    {
        $value = self::faker()->sentence();

        $exception = Exception\InvalidJsonPointer::fromJsonString($value);

        $message = \sprintf(
            'Value "%s" does not appear to be a valid JSON string representation of a JSON Pointer.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testFromUriFragmentIdentifierStringReturnsInvalidJsonPointerException(): void
    {
        $value = self::faker()->sentence();

        $exception = Exception\InvalidJsonPointer::fromUriFragmentIdentifierString($value);

        $message = \sprintf(
            'Value "%s" does not appear to be a valid URI fragment identifier representation of a JSON Pointer.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
