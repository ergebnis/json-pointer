<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-pointer
 */

namespace Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class JsonPointers
{
    /**
     * @var array<int, JsonPointer>
     */
    private array $jsonPointers;

    private function __construct(JsonPointer ...$jsonPointers)
    {
        $this->jsonPointers = $jsonPointers;
    }

    public static function create(JsonPointer ...$jsonPointers): self
    {
        return new self(...$jsonPointers);
    }

    public function contains(JsonPointer $other): bool
    {
        return \in_array(
            $other,
            $this->jsonPointers,
            false,
        );
    }
}
