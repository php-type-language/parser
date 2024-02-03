<a href="https://github.com/php-type-language" target="_blank">
    <picture>
        <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
    </picture>
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

The PHP reference implementation for Type Language Parser.

**Type Language** is a declarative type language inspired by static analyzers
like [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/).

Read [documentation pages](https://phpdoc.io) for more information.

## Installation

Type Language Parser is available as composer repository and can be installed
using the following command in a root of your project:

```sh
$ composer require type-lang/parser
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
TypeLang\Parser\Node\Stmt\NamedTypeNode {#739
  +offset: 0
  +name: TypeLang\Parser\Node\Name {#737
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {#746
        +offset: 0
        +value: "array"
      }
    ]
  }
  +arguments: null
  +fields: TypeLang\Parser\Node\Stmt\Shape\FieldsListNode {#757
    +offset: 11
    +items: array:1 [
      0 => TypeLang\Parser\Node\Stmt\Shape\NamedFieldNode {#346
        +offset: 11
        +type: TypeLang\Parser\Node\Stmt\CallableTypeNode {#339
          +offset: 16
          +name: TypeLang\Parser\Node\Name {#313
            +offset: 16
            -parts: array:1 [
              0 => TypeLang\Parser\Node\Identifier {#332
                +offset: 16
                +value: "callable"
              }
            ]
          }
          +parameters: TypeLang\Parser\Node\Stmt\Callable\ParametersListNode {#318
            +offset: 25
            +items: array:2 [
              0 => TypeLang\Parser\Node\Stmt\Callable\ParameterNode {#315
                +offset: 25
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#304
                  +offset: 25
                  +name: TypeLang\Parser\Node\Name {#242
                    +offset: 25
                    -parts: array:1 [
                      0 => TypeLang\Parser\Node\Identifier {#311
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
              1 => TypeLang\Parser\Node\Stmt\Callable\ParameterNode {#330
                +offset: 34
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#312
                  +offset: 34
                  +name: TypeLang\Parser\Node\Name {#284
                    +offset: 34
                    -parts: array:1 [
                      0 => TypeLang\Parser\Node\Identifier {#298
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
          +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#287
            +offset: 40
            +name: TypeLang\Parser\Node\Name {#314
              +offset: 40
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {#307
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
        +key: TypeLang\Parser\Node\Identifier {#371
          +offset: 11
          +value: "key"
        }
      }
    ]
    +sealed: false
  }
}
```

### Array Normalization

```php
$parser = new \TypeLang\Parser\Parser();

$type = $parser->parse(<<<'PHP'
    array{
        key: callable(Example, int): mixed,
        ...
    }
    PHP);

var_dump($type->toArray());
```

Expected Output:

```php
array:3 [
  "kind" => TypeLang\Parser\Node\Stmt\TypeKind {#773
    +name: "TYPE_KIND"
    +value: 1
  }
  "name" => "array"
  "fields" => array:2 [
    "items" => array:1 [
      0 => array:4 [
        "kind" => TypeLang\Parser\Node\Stmt\Shape\ShapeFieldKind {#733
          +name: "NAMED_FIELD_KIND"
          +value: 1
        }
        "type" => array:4 [
          "kind" => TypeLang\Parser\Node\Stmt\TypeKind {#741
            +name: "CALLABLE_KIND"
            +value: 2
          }
          "name" => "callable"
          "parameters" => array:1 [
            "items" => array:2 [
              0 => array:5 [
                "name" => null
                "type" => array:2 [
                  "kind" => TypeLang\Parser\Node\Stmt\TypeKind {#773}
                  "name" => "Example"
                ]
                "output" => false
                "variadic" => false
                "optional" => false
              ]
              1 => array:5 [
                "name" => null
                "type" => array:2 [
                  "kind" => TypeLang\Parser\Node\Stmt\TypeKind {#773}
                  "name" => "int"
                ]
                "output" => false
                "variadic" => false
                "optional" => false
              ]
            ]
          ]
          "type" => array:2 [
            "kind" => TypeLang\Parser\Node\Stmt\TypeKind {#773}
            "name" => "mixed"
          ]
        ]
        "optional" => false
        "key" => "key"
      ]
    ]
    "sealed" => false
  ]
]
```

### JSON Serialization


```php
$parser = new \TypeLang\Parser\Parser();

$type = $parser->parse(<<<'PHP'
    array{
        key: callable(Example, int): mixed,
        ...
    }
    PHP);

echo json_encode($type);
```

Expected Output:

```json
{
    "kind": 1,
    "name": "array",
    "fields": {
        "items": [
            {
                "kind": 1,
                "type": {
                    "kind": 2,
                    "name": "callable",
                    "parameters": {
                        "items": [
                            {
                                "name": null,
                                "type": {
                                    "kind": 1,
                                    "name": "Example"
                                },
                                "output": false,
                                "variadic": false,
                                "optional": false
                            },
                            {
                                "name": null,
                                "type": {
                                    "kind": 1,
                                    "name": "int"
                                },
                                "output": false,
                                "variadic": false,
                                "optional": false
                            }
                        ]
                    },
                    "type": {
                        "kind": 1,
                        "name": "mixed"
                    }
                },
                "optional": false,
                "key": "key"
            }
        ],
        "sealed": false
    }
}
```
