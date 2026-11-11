<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
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
    private string $jsonString;

    private function __construct(
        string $jsonString,
        ReferenceToken ...$referenceTokens
    ) {
        $this->jsonString = $jsonString;
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
        if (1 !== \preg_match(Pattern::JSON_STRING_JSON_POINTER, $value)) {
            throw Exception\InvalidJsonPointer::fromJsonString($value);
        }

        $jsonStringValues = \array_slice(
            \explode('/', $value),
            1,
        );

        return new self(
            $value,
            ...\array_map(static function (string $jsonStringValue): ReferenceToken {
                return ReferenceToken::fromString(\str_replace(
                    [
                        '~1',
                        '~0',
                    ],
                    [
                        '/',
                        '~',
                    ],
                    $jsonStringValue,
                ));
            }, $jsonStringValues),
        );
    }

    public static function fromReferenceTokens(ReferenceToken ...$referenceTokens): self
    {
        return new self(
            self::jsonStringFromReferenceTokens(...$referenceTokens),
            ...$referenceTokens,
        );
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-3
     * @see https://datatracker.ietf.org/doc/html/rfc6901#section-5
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-3.5
     *
     * @throws Exception\InvalidJsonPointer
     */
    public static function fromUriFragmentIdentifierString(string $value): self
    {
        if (1 !== \preg_match(Pattern::URI_FRAGMENT_IDENTIFIER_JSON_POINTER, $value)) {
            throw Exception\InvalidJsonPointer::fromJsonString($value);
        }

        $uriFragmentIdentifierStringValues = \array_slice(
            \explode('/', $value),
            1,
        );

        $referenceTokens = \array_map(static function (string $uriFragmentIdentifierStringValue): ReferenceToken {
            return ReferenceToken::fromString(\str_replace(
                [
                    '~1',
                    '~0',
                ],
                [
                    '/',
                    '~',
                ],
                \rawurldecode($uriFragmentIdentifierStringValue),
            ));
        }, $uriFragmentIdentifierStringValues);

        return new self(
            self::jsonStringFromReferenceTokens(...$referenceTokens),
            ...$referenceTokens,
        );
    }

    public static function document(): self
    {
        return new self('');
    }

    public function append(ReferenceToken $referenceToken): self
    {
        $referenceTokens = $this->referenceTokens;

        $referenceTokens[] = $referenceToken;

        return new self(
            \sprintf(
                '%s/%s',
                $this->jsonString,
                $referenceToken->toJsonString(),
            ),
            ...$referenceTokens,
        );
    }

    public function toJsonString(): string
    {
        return $this->jsonString;
    }

    public function toUriFragmentIdentifierString(): string
    {
        if ([] === $this->referenceTokens) {
            return '#';
        }

        return \sprintf(
            '#/%s',
            \implode('/', \array_map(static function (ReferenceToken $referenceToken): string {
                return $referenceToken->toUriFragmentIdentifierString();
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
        return $this->jsonString === $other->jsonString;
    }

    private static function jsonStringFromReferenceTokens(ReferenceToken ...$referenceTokens): string
    {
        if ([] === $referenceTokens) {
            return '';
        }

        return \sprintf(
            '/%s',
            \implode('/', \array_map(static function (ReferenceToken $referenceToken): string {
                return $referenceToken->toJsonString();
            }, $referenceTokens)),
        );
    }
}
