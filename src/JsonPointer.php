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
final class JsonPointer
{
    private string $jsonStringValue;

    private function __construct(string $jsonStringValue)
    {
        $this->jsonStringValue = $jsonStringValue;
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @throws Exception\InvalidJsonPointer
     */
    public static function fromJsonString(string $value): self
    {
        if (1 !== \preg_match('/^(\/(?P<referenceToken>((?P<unescaped>[\x00-\x2E]|[\x30-\x7D]|[\x7F-\x{10FFFF}])|(?P<escaped>~[01]))*))*$/u', $value, $matches)) {
            throw Exception\InvalidJsonPointer::fromJsonString($value);
        }

        return new self($value);
    }

    public static function document(): self
    {
        return new self('');
    }

    public function append(ReferenceToken $referenceToken): self
    {
        return new self(\sprintf(
            '%s/%s',
            $this->jsonStringValue,
            $referenceToken->toJsonString(),
        ));
    }

    public function toJsonString(): string
    {
        return $this->jsonStringValue;
    }

    public function equals(self $other): bool
    {
        return $this->jsonStringValue === $other->jsonStringValue;
    }
}
