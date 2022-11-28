# json-pointer

[![Integrate](https://github.com/ergebnis/json-pointer/workflows/Integrate/badge.svg)](https://github.com/ergebnis/json-pointer/actions)
[![Prune](https://github.com/ergebnis/json-pointer/workflows/Prune/badge.svg)](https://github.com/ergebnis/json-pointer/actions)
[![Release](https://github.com/ergebnis/json-pointer/workflows/Release/badge.svg)](https://github.com/ergebnis/json-pointer/actions)
[![Renew](https://github.com/ergebnis/json-pointer/workflows/Renew/badge.svg)](https://github.com/ergebnis/json-pointer/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/json-pointer/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/json-pointer)
[![Type Coverage](https://shepherd.dev/github/ergebnis/json-pointer/coverage.svg)](https://shepherd.dev/github/ergebnis/json-pointer)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/json-pointer/v/stable)](https://packagist.org/packages/ergebnis/json-pointer)
[![Total Downloads](https://poser.pugx.org/ergebnis/json-pointer/downloads)](https://packagist.org/packages/ergebnis/json-pointer)

Provides [JSON pointer](https://datatracker.ietf.org/doc/html/rfc6901) as a value object.

## Installation

Run

```sh
composer require ergebnis/json-pointer
```

## Usage

### `ReferenceToken`

You can create a `ReferenceToken` from a `string` value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromString('foo/9000/😆');

$referenceToken->toJsonString();                  // 'foo~19000~😆'
$referenceToken->toString();                      // 'foo/9000/😆'
$referenceToken->toUriFragmentIdentifierString(); // 'foo~19000~1%F0%9F%98%86'
```

You can create a `ReferenceToken` from a [JSON `string` value](https://datatracker.ietf.org/doc/html/rfc6901#section-5):

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromJsonString('foo~19000~😆');

$referenceToken->toJsonString();                  // 'foo~19000~😆'
$referenceToken->toString();                      // 'foo/9000/😆'
$referenceToken->toUriFragmentIdentifierString(); // 'foo~19000~1%F0%9F%98%86'
```

You can create a `ReferenceToken` from a [URI fragment identifier `string` value](https://datatracker.ietf.org/doc/html/rfc6901#section-6):

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromUriFragmentIdentifierString('foo~19000~1%F0%9F%98%86');

$referenceToken->toJsonString();                  // 'foo~19000~😆'
$referenceToken->toString();                      // 'foo/9000/😆'
$referenceToken->toUriFragmentIdentifierString(); // 'foo~19000~1%F0%9F%98%86'
```

You can create a `ReferenceToken` from an `int` value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromInt(9001);

$referenceToken->toJsonString();                  // '9001'
$referenceToken->toString();                      // '9001'
$referenceToken->toUriFragmentIdentifierString(); // '9001'
```

You can compare `ReferenceToken`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$one = Pointer\ReferenceToken::fromString('foo/bar');
$two = Pointer\ReferenceToken::fromJsonString('foo~1bar');
$three = Pointer\ReferenceToken::fromString('foo/9000');

$one->equals($two);   // true
$one->equals($three); // false
```

### `JsonPointer`

You can create a `JsonPointer` referencing a document:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::document();

$jsonPointer->toJsonString();                  // ''
$jsonPointer->toUriFragmentIdentifierString(); // '#'
```

You can create a `JsonPointer` from a [JSON `string` representation](https://datatracker.ietf.org/doc/html/rfc6901#section-5) value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar/😆');

$jsonPointer->toJsonString();                  // '/foo/bar/😆'
$jsonPointer->toUriFragmentIdentifierString(); // '#/foo/bar/%F0%9F%98%86'
```

You can create a `JsonPointer` from a [URI fragment identifier `string` representation](https://datatracker.ietf.org/doc/html/rfc6901#section-6) value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::fromUriFragmentIdentifierString('#/foo/bar/%F0%9F%98%86');

$jsonPointer->toJsonString();                  // '/foo/bar/😆'
$jsonPointer->toUriFragmentIdentifierString(); // '#/foo/bar/%F0%9F%98%86'
```

You can create a `JsonPointer` from `ReferenceToken`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceTokens = [
    Pointer\ReferenceToken::fromString('foo'),
    Pointer\ReferenceToken::fromString('bar'),
];

$jsonPointer = Pointer\JsonPointer::fromReferenceTokens(...$referenceTokens);

$jsonPointer->toJsonString();                  // '/foo/bar'
$jsonPointer->toUriFragmentIdentifierString(); // '#/foo/bar'
```

You can compare `JsonPointer`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$one = Pointer\JsonPointer::fromJsonString('/foo/bar');
$two = Pointer\JsonPointer::fromJsonString('/foo~1bar');
$three = Pointer\JsonPointer::fromUriFragmentIdentifierString('#/foo/bar');

$one->equals($two);   // false
$one->equals($three); // true
```

You can append a `ReferenceToken` to a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

$referenceToken = Pointer\ReferenceToken::fromString('baz');

$newJsonPointer = $jsonPointer->append($referenceToken);

$newJsonPointer->toJsonString();                  // '/foo/bar/baz'
$newJsonPointer->toUriFragmentIdentifierString(); // '#foo/bar/baz'
```

### `Specification`

You can create a `Specification` that is always satisfied by a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::always();

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // true
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // true
```

You can create a `Specification` that is satisfied when a closure returns `true` for a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::closure(static function (Pointer\JsonPointer $jsonPointer) {
    return $jsonPointer->toJsonString() === '/foo/bar';
});

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // false
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // true
```

You can create a `Specification` that is satisfied when a `JsonPointer` equals another `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/bar'));

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // false
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // true
```

You can create a `Specification` that is never satisfied by a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::never();

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // false
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // false
```

You can create a `Specification` that is satisfied when another `Specification` is not satisfied by a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::not(Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/bar')));

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // true
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // false
```

You can compose `Specification`s to find out if a `JsonPointer` satisfies any of them:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$specification = Pointer\Specification::anyOf(
    Pointer\Specification::closure(static function(Pointer\JsonPointer $jsonPointer) {
        return $jsonPointer->toJsonString() === '/foo/bar';
    }),
    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/foo/baz')),
    Pointer\Specification::never(),
);

$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo'));     // false
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/bar')); // true
$specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/foo/baz')); // true
```
## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Curious what I am building?

:mailbox_with_mail: [Subscribe to my list](https://localheinz.com/projects/), and I will occasionally send you an email to let you know what I am working on.
