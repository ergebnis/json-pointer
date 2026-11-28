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

namespace Ergebnis\Json\Pointer\Test\Unit;

use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Pointer\Specification
 *
 * @uses \Ergebnis\Json\Pointer\JsonPointer
 * @uses \Ergebnis\Json\Pointer\ReferenceToken
 */
final class SpecificationTest extends Framework\TestCase
{
    use Pointer\Test\Util\Helper;

    public function testAlwaysIsAlwaysSatisfiedByAnyJsonPointer(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::always();

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenEmpty(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar/baz');

        $specification = Pointer\Specification::anyOf();

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenNoneOfTheSpecificationsAreSatisfiedByJsonPointer(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar/baz');

        $specification = Pointer\Specification::anyOf(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/bar')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/baz')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/qux')),
        );

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testAnyOfIsNotSatisfiedByJsonPointerWhenAnyOfTheSpecificationsIsSatisfiedByJsonPointer(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/baz');

        $specification = Pointer\Specification::anyOf(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/bar')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/baz')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/qux')),
        );

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }

    public function testClosureIsNotSatisfiedWhenClosureReturnsFalse(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::closure(static function (): bool {
            return false;
        });

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testClosureIsSatisfiedWhenClosureReturnsTrue(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::closure(static function (): bool {
            return true;
        });

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }

    public function testEqualsIsNotSatisfiedByJsonPointerWhenJsonPointerDoesNotEqualOther(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar/baz');

        $other = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::equals($other);

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testEqualsIsSatisfiedByJsonPointerWhenJsonPointerEqualsOther(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $other = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::equals($other);

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }

    public function testNeverIsNotSatisfiedByAnyJsonPointer(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

        $specification = Pointer\Specification::never();

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testNotIsNotSatisfiedByJsonPointerWhenJsonPointerSatisfiesSpecification(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo');

        $specification = Pointer\Specification::not(Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo')));

        self::assertFalse($specification->isSatisfiedBy($jsonPointer));
    }

    public function testNotIsSatisfiedByJsonPointerWhenJsonPointerDoesNotSatisfySpecification(): void
    {
        $jsonPointer = Pointer\JsonPointer::fromJsonString('/foo');

        $specification = Pointer\Specification::not(Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/bar')));

        self::assertTrue($specification->isSatisfiedBy($jsonPointer));
    }
}
