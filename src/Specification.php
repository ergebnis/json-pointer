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

final class Specification
{
    /**
     * @psalm-var \Closure(JsonPointer):bool
     */
    private \Closure $closure;

    /**
     * @param \Closure(JsonPointer):bool $closure
     */
    private function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function isSatisfiedBy(JsonPointer $jsonPointer): bool
    {
        $closure = $this->closure;

        return $closure($jsonPointer);
    }

    public static function equals(JsonPointer $other): self
    {
        return new self(static function (JsonPointer $jsonPointer) use ($other) {
            return $jsonPointer->equals($other);
        });
    }
}
