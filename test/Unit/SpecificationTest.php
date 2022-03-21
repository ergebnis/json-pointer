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
use Ergebnis\Json\Pointer\Specification;
use Ergebnis\Json\Pointer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Pointer\Specification
 *
 * @uses \Ergebnis\Json\Pointer\JsonPointer
 * @uses \Ergebnis\Json\Pointer\ReferenceToken
 */
final class SpecificationTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testEqualsIsNotSatisifedByJsonPointerWhenJsonPointerDoesNotEqualOther(): void
    {
        $jsonPointer = JsonPointer::fromJsonString('/foo/bar/baz');

        $other = JsonPointer::fromJsonString('/foo/bar');

        $specification = Specification::equals($other);

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testEqualsIsSatisifedByJsonPointerWhenJsonPointerEqualsOther(): void
    {
        $jsonPointer = JsonPointer::fromJsonString('/foo/bar');

        $other = JsonPointer::fromJsonString('/foo/bar');

        $specification = Specification::equals($other);

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenEmpty(): void
    {
        $jsonPointer = JsonPointer::fromJsonString('/foo/bar/baz');

        $specification = Specification::anyOf();

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenNoneOfTheSpecificationsAreSatisfiedByJsonPointer(): void
    {
        $jsonPointer = JsonPointer::fromJsonString('/foo/bar/baz');

        $specification = Specification::anyOf(
            Specification::equals(JsonPointer::fromJsonString('/foo/bar')),
            Specification::equals(JsonPointer::fromJsonString('/foo/baz')),
            Specification::equals(JsonPointer::fromJsonString('/foo/qux')),
        );

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenAnyOfTheSpecificationsIsSatisfiedByJsonPointer(): void
    {
        $jsonPointer = JsonPointer::fromJsonString('/foo/baz');

        $specification = Specification::anyOf(
            Specification::equals(JsonPointer::fromJsonString('/foo/bar')),
            Specification::equals(JsonPointer::fromJsonString('/foo/baz')),
            Specification::equals(JsonPointer::fromJsonString('/foo/qux')),
        );

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }
}
