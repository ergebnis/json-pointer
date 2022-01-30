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

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\Json\Pointer\JsonPointer;
use Ergebnis\Json\Pointer\JsonPointers;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\JsonPointers
 *
 * @uses \Ergebnis\Json\Pointer\JsonPointer
 * @uses \Ergebnis\Json\Pointer\ReferenceToken
 */
final class JsonPointersTest extends Framework\TestCase
{
    public function testContainsReturnsFalseWhenJsonPointersIsEmpty(): void
    {
        $jsonPointers = JsonPointers::create();

        self::assertFalse($jsonPointers->contains(JsonPointer::fromJsonString('/foo')));
    }

    public function testContainsReturnsFalseWhenJsonPointersDoesNotContainJsonPointer(): void
    {
        $jsonPointers = JsonPointers::create(
            JsonPointer::fromJsonString('/foo/bar'),
            JsonPointer::fromJsonString('/foo/9000'),
        );

        self::assertFalse($jsonPointers->contains(JsonPointer::fromJsonString('/foo')));
    }

    public function testContainsReturnsTrueWhenJsonPointersContainsJsonPointer(): void
    {
        $jsonPointers = JsonPointers::create(
            JsonPointer::fromJsonString('/foo/bar'),
            JsonPointer::fromJsonString('/foo/9000'),
        );

        self::assertTrue($jsonPointers->contains(JsonPointer::fromJsonString('/foo/9000')));
    }
}
