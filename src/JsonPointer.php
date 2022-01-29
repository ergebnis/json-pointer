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
    /**
     * @var array<int, ReferenceToken>
     */
    private array $referenceTokens;

    private function __construct(ReferenceToken ...$referenceTokens)
    {
        $this->referenceTokens = $referenceTokens;
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     *
     * @throws Exception\InvalidJsonPointer
     */
    public static function fromJsonString(string $value): self
    {
        if (1 !== \preg_match(Pattern::JSON_POINTER_JSON_STRING, $value)) {
            throw Exception\InvalidJsonPointer::fromJsonString($value);
        }

        $jsonStringValues = \array_slice(
            \explode('/', $value),
            1,
        );

        return new self(...\array_map(static function (string $jsonStringValue): ReferenceToken {
            return ReferenceToken::fromJsonString($jsonStringValue);
        }, $jsonStringValues));
    }

    public static function fromReferenceTokens(ReferenceToken ...$referenceTokens): self
    {
        return new self(...$referenceTokens);
    }

    public static function document(): self
    {
        return new self();
    }

    public function append(ReferenceToken $referenceToken): self
    {
        $referenceTokens = $this->referenceTokens;

        $referenceTokens[] = $referenceToken;

        return new self(...$referenceTokens);
    }

    public function toJsonString(): string
    {
        if ([] === $this->referenceTokens) {
            return '';
        }

        return \sprintf(
            '/%s',
            \implode('/', \array_map(static function (ReferenceToken $referenceToken): string {
                return $referenceToken->toJsonString();
            }, $this->referenceTokens)),
        );
    }

    /**
     * @return array<int, ReferenceToken>
     */
    public function toReferenceTokens(): array
    {
        return $this->referenceTokens;
    }

    public function equals(self $other): bool
    {
        return $this->referenceTokens == $other->referenceTokens;
    }
}
