<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

---

<p align="center">
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/parser"><img src="https://poser.pugx.org/type-lang/parser/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/parser/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/parser/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/parser/actions"><img src="https://github.com/php-type-language/parser/workflows/tests/badge.svg"></a>
</p>

Reference implementation for TypeLang Parser.

**TypeLang** is a declarative type language inspired by static analyzers
like [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/).

Read [documentation pages](https://typelang.dev) for more information.

## Installation

TypeLang Parser is available as Composer repository and can be installed
using the following command in a root of your project:

```sh
composer require type-lang/parser
```

## Quick Start

```php
$parser = new \TypeLang\Parser\Parser();

$type = $parser->parse(<<<'PHP'
    array{
        key: callable(Example, int): mixed,
        ...
    }
    PHP);

var_dump($type);
```

Expected Output:

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "array"
      }
    ]
  }
  +arguments: null
  +fields: TypeLang\Parser\Node\Stmt\Shape\FieldsListNode {
    +offset: 11
    +items: array:1 [
      0 => TypeLang\Parser\Node\Stmt\Shape\NamedFieldNode {
        +offset: 11
        +type: TypeLang\Parser\Node\Stmt\CallableTypeNode {
          +offset: 16
          +name: TypeLang\Parser\Node\Name {
            +offset: 16
            -parts: array:1 [
              0 => TypeLang\Parser\Node\Identifier {
                +offset: 16
                +value: "callable"
              }
            ]
          }
          +parameters: TypeLang\Parser\Node\Stmt\Callable\ParametersListNode {
            +offset: 25
            +items: array:2 [
              0 => TypeLang\Parser\Node\Stmt\Callable\ParameterNode {
                +offset: 25
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {
                  +offset: 25
                  +name: TypeLang\Parser\Node\Name {
                    +offset: 25
                    -parts: array:1 [
                      0 => TypeLang\Parser\Node\Identifier {
                        +offset: 25
                        +value: "Example"
                      }
                    ]
                  }
                  +arguments: null
                  +fields: null
                }
                +name: null
                +output: false
                +variadic: false
                +optional: false
              }
              1 => TypeLang\Parser\Node\Stmt\Callable\ParameterNode {
                +offset: 34
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {
                  +offset: 34
                  +name: TypeLang\Parser\Node\Name {
                    +offset: 34
                    -parts: array:1 [
                      0 => TypeLang\Parser\Node\Identifier {
                        +offset: 34
                        +value: "int"
                      }
                    ]
                  }
                  +arguments: null
                  +fields: null
                }
                +name: null
                +output: false
                +variadic: false
                +optional: false
              }
            ]
          }
          +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {
            +offset: 40
            +name: TypeLang\Parser\Node\Name {
              +offset: 40
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {
                  +offset: 40
                  +value: "mixed"
                }
              ]
            }
            +arguments: null
            +fields: null
          }
        }
        +optional: false
        +key: TypeLang\Parser\Node\Identifier {
          +offset: 11
          +value: "key"
        }
      }
    ]
    +sealed: false
  }
}
```
