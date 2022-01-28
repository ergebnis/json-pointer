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

namespace Ergebnis\Json\Pointer\Test\Unit\Exception;

use Ergebnis\Json\Pointer\Exception;
use Ergebnis\Json\Pointer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\Exception\InvalidJsonPointer
 */
final class InvalidJsonPointerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromValueReturnsInvalidJsonPointerException(): void
    {
        $value = self::faker()->sentence();

        $exception = Exception\InvalidJsonPointer::fromString($value);

        $message = \sprintf(
            'Value "%s" does not appear to be a valid JSON Pointer.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
