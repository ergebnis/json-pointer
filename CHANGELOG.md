# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`2.0.0...main`][2.0.0...main].

## [`2.0.0`][2.0.0]

For a full diff see [`1.0.0...2.0.0`][1.0.0...2.0.0].

## Changed

- Renamed named constructors and accessors of `Exception\InvalidJsonPointer`, `JsonPointer`, and `ReferenceToken` ([#4]) and ([#5]), by [@localheinz]

  - `Exception\InvalidJsonPointer::fromString()` to `Exception\InvalidJsonPointer::fromJsonString()`
  - `JsonPointer::fromString()` to `JsonPointer::fromJsonString()`
  - `JsonPointer::toString()` to `JsonPointer::toJsonString()`
  - `ReferenceToken::fromEscapedString()` to `ReferenceToken::fromJsonString()`
  - `ReferenceToken::fromUnescapedString()` to `ReferenceToken::fromString()`
  - `ReferenceToken::toEscapedString()` to `ReferenceToken::toJsonString()`
  - `ReferenceToken::toUnescapedString()` to `ReferenceToken::toString()`

## [`1.0.0`][1.0.0]

For a full diff see [`a5ba52c...1.0.0`][a5ba52c...1.0.0].

### Added

- Added `ReferenceToken` as a value object ([#1]), by [@localheinz]
- Added `JsonPointer` as a value object ([#2]), by [@localheinz]

[a5ba52c...1.0.0]: https://github.com/ergebnis/json-pointer/compare/a5ba52c...1.0.0
[1.0.0...main]: https://github.com/ergebnis/json-pointer/compare/1.0.0...main
[2.0.0...main]: https://github.com/ergebnis/json-pointer/compare/2.0.0...main

[#1]: https://github.com/ergebnis/json-pointer/pull/1
[#2]: https://github.com/ergebnis/json-pointer/pull/2
[#4]: https://github.com/ergebnis/json-pointer/pull/4
[#5]: https://github.com/ergebnis/json-pointer/pull/5

[@localheinz]: https://github.com/localheinz
