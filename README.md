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

$referenceToken = Pointer\ReferenceToken::fromString('foo/bar');

$referenceToken->toJsonString(); // 'foo~1bar'
$referenceToken->toString();     // 'foo/bar'
```

You can create a `ReferenceToken` from a [JSON `string` value](https://datatracker.ietf.org/doc/html/rfc6901#section-5):

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromJsonString('foo~1bar');

$referenceToken->toJsonString(); // 'foo~1bar'
$referenceToken->toString();     // 'foo/bar'
```

You can create a `ReferenceToken` from an `int` value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$referenceToken = Pointer\ReferenceToken::fromInt(9001);

$referenceToken->toJsonString(); // '9001'
$referenceToken->toString();     // '9001'
```

You can compare `ReferenceToken`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$two = Pointer\ReferenceToken::fromJsonString('foo~1bar');
$one = Pointer\ReferenceToken::fromString('foo/bar');

$one->equals($two); // true
```

### `JsonPointer`

You can create a `JsonPointer` referencing a document:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::document();

$jsonPointer->toJsonString(); // ''
```

You can create a `JsonPointer` from a [JSON `string` representation](https://datatracker.ietf.org/doc/html/rfc6901#section-5) value:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

$jsonPointer->toJsonString(); // '/foo/bar'
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

$jsonPointer->toJsonString(); // '/foo/bar'
```
You can compare `JsonPointer`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$one = Pointer\JsonPointer::fromJsonString('/foo/bar');
$two = Pointer\JsonPointer::fromJsonString('/foo~1bar');

$one->equals($two); // false
```

You can append a `ReferenceToken` to a `JsonPointer`:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Pointer;

$jsonPointer = Pointer\JsonPointer::fromJsonString('/foo/bar');

$referenceToken = Pointer\ReferenceToken::fromString('baz');

$newJsonPointer = $jsonPointer->append($referenceToken);

$newJsonPointer->toJsonString(); // '/foo/bar/baz'
```

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Curious what I am building?

:mailbox_with_mail: [Subscribe to my list](https://localheinz.com/projects/), and I will occasionally send you an email to let you know what I am working on.
