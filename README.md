<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/require/php?style=for-the-badge" alt="PHP 8.4+"></a>
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/parser/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/parser/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/parser/actions/workflows/tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/php-type-language/parser/tests.yml?label=Tests&style=flat-square&logo=unpkg"></a>
</p>

---

The reference parser for **TypeLang** — a declarative type language inspired by
static analyzers like [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/).

It reads a type declaration string and builds an AST of `TypeLang\Type\*` nodes,
checking the grammar along the way. The node classes themselves live in the
separate [`type-lang/types`](https://packagist.org/packages/type-lang/types) package.

- Full documentation is [available at typelang.dev](https://typelang.dev).
- Language specification is [available here](https://typelang.dev/static/spec.html).

## Installation

Install the package via [Composer](https://getcomposer.org):

```sh
composer require type-lang/parser
```

**Requirements:** 
- PHP 8.4+

## Usage

`TypeLang\Parser\TypeParser` is the entry point. Its `parse()` method turns a
type declaration into a `TypeLang\Type\TypeNode`:

```php
$parser = new TypeLang\Parser\TypeParser();

$type = $parser->parse('array{ key: int }');

var_dump($type);
// object(TypeLang\Type\NamedTypeNode) {
//   ["name"]   => "array"
//   ["fields"] => object(TypeLang\Type\Shape\FieldsListNode) { ... }
//   ...
// }
```

Every node exposes an `$offset` (byte offset in the source) plus a handful of
properties describing its kind.

### Strict vs. Tolerant Parsing

- `parse(): TypeNode` — strict mode; requires the whole input to be a valid
  type and throws a `ParserExceptionInterface` on the first error.
- `parseTolerant(): ParsedResult` — parses as much as it can and reports how far
  it consumed the source. Useful for phpdoc, where a type is followed by a
  free-text description.

```php
$result = $parser->parseTolerant('int and some trailing description');

$result->type;   // TypeLang\Type\NamedTypeNode ("int")
$result->offset; // offset where parsing stopped
```

See the [documentation](https://typelang.dev) for feature toggling, visitors and
name resolution.
