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

namespace Ergebnis\Json\Pointer;

/**
 * @internal
 */
final class Pattern
{
    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    public const JSON_POINTER_JSON_STRING = '/^(?P<jsonPointer>(\/(?P<referenceToken>((?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])|(?P<escaped>~[01]))*))*)$/u';

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     */
    public const REFERENCE_TOKEN = '/^(?P<referenceToken>((?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])|(?P<escaped>~[01]))*)$/u';
}
