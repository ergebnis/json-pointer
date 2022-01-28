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
 * @psalm-immutable
 *
 * @see https://datatracker.ietf.org/doc/html/rfc6901
 */
final class ReferenceToken
{
    private string $escapedValue;

    private function __construct(string $escapedValue)
    {
        $this->escapedValue = $escapedValue;
    }

    /**
     * @throws Exception\InvalidReferenceToken
     */
    public static function fromInt(int $value): self
    {
        if (0 > $value) {
            throw Exception\InvalidReferenceToken::fromInt($value);
        }

        return new self((string) $value);
    }

    /**
     * @throws Exception\InvalidReferenceToken
     */
    public static function fromEscapedString(string $value): self
    {
        if (1 !== \preg_match('/^(?P<referenceToken>((?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])|(?P<escaped>~[01]))*)$/u', $value)) {
            throw Exception\InvalidReferenceToken::fromString($value);
        }

        return new self($value);
    }

    public static function fromUnescapedString(string $value): self
    {
        return self::fromEscapedString(\str_replace(
            [
                '~',
                '/',
            ],
            [
                '~0',
                '~1',
            ],
            $value,
        ));
    }

    public function toEscapedString(): string
    {
        return $this->escapedValue;
    }

    public function toUnescapedString(): string
    {
        return \str_replace(
            [
                '~1',
                '~0',
            ],
            [
                '/',
                '~',
            ],
            $this->escapedValue,
        );
    }

    public function equals(self $other): bool
    {
        return $this->escapedValue === $other->escapedValue;
    }
}
