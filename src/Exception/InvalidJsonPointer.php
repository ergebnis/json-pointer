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

namespace Ergebnis\Json\Pointer\Exception;

final class InvalidJsonPointer extends \InvalidArgumentException implements Exception
{
    public static function fromString(string $value): self
    {
        return new self(\sprintf(
            'Value "%s" does not appear to be a valid JSON Pointer.',
            $value,
        ));
    }
}
