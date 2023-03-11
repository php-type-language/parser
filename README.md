<a href="https://github.com/php-type-language" target="_blank">
    <picture>
        <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
    </picture>
</a>

---

<p align="center">
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/parser/blob/master/LICENSE"><img src="https://poser.pugx.org/phptl/parser/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/parser/actions"><img src="https://github.com/php-type-language/parser/workflows/build/badge.svg"></a>
</p>

The PHP reference implementation for Type Language.

## Getting Started

**Type Language** is a declarative type language inspired by static analyzers
like [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/).

The Type Language specification is edited in the markdown files found in
[/spec](https://github.com/php-type-language/spec) the latest release of which
is published at https://php-type-language.github.io/spec/.

The latest draft specification can be found at
https://php-type-language.github.io/spec/draft/ which tracks the latest commit
to the master branch in [spec](https://github.com/php-type-language/spec)
repository.

## Installation

Type Language Parser is available as composer repository and can be installed
using the following command in a root of your project:

```sh
$ composer require phptl/parser
```

## Quick Start

```php
$parser = new \TypeLang\Parser\Parser();

$ast = $parser->parse(<<<'PHP'
    array{
        field1: callable(Example, int):mixed,
        field2: list<Some>,
        field3: iterable<array-key, array{int, non-empty-string}>,
        Some::CONST_*,
        ...
    }
PHP);
```

Expected Output:

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {#582
  +offset: 4
  +name: TypeLang\Parser\Node\Name {#576
    +offset: 4
    +name: "array"
    +isSimple: true
    +parts: array:1 [
      0 => "array"
    ]
  }
  +parameters: null
  +arguments: TypeLang\Parser\Node\Stmt\Shape\ArgumentsListNode {#575
    +offset: 9
    +list: array:4 [
      0 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#578
        +offset: 19
        +value: TypeLang\Parser\Node\Stmt\CallableTypeNode {#370
          +offset: 27
          +name: TypeLang\Parser\Node\Name {#359
            +offset: 27
            +name: "callable"
            +isSimple: true
            +parts: array:1 [
              0 => "callable"
            ]
          }
          +arguments: TypeLang\Parser\Node\Stmt\Callable\ArgumentsListNode {#358
            +offset: 36
            +list: array:2 [
              0 => TypeLang\Parser\Node\Stmt\Callable\ArgumentNode {#268
                +offset: 36
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#192
                  +offset: 36
                  +name: TypeLang\Parser\Node\Name {#143
                    +offset: 36
                    +name: "Example"
                    +isSimple: true
                    +parts: array:1 [
                      0 => "Example"
                    ]
                  }
                  +parameters: null
                  +arguments: null
                }
                +modifier: null
              }
              1 => TypeLang\Parser\Node\Stmt\Callable\ArgumentNode {#276
                +offset: 45
                +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#482
                  +offset: 45
                  +name: TypeLang\Parser\Node\Name {#483
                    +offset: 45
                    +name: "int"
                    +isSimple: true
                    +parts: array:1 [
                      0 => "int"
                    ]
                  }
                  +parameters: null
                  +arguments: null
                }
                +modifier: null
              }
            ]
          }
          +type: TypeLang\Parser\Node\Stmt\NamedTypeNode {#154
            +offset: 50
            +name: TypeLang\Parser\Node\Name {#126
              +offset: 50
              +name: "mixed"
              +isSimple: true
              +parts: array:1 [
                0 => "mixed"
              ]
            }
            +parameters: null
            +arguments: null
          }
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#555
          +offset: 19
          +value: "field1"
        }
        +optional: false
      }
      1 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#556
        +offset: 65
        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#275
          +offset: 73
          +name: TypeLang\Parser\Node\Name {#274
            +offset: 73
            +name: "list"
            +isSimple: true
            +parts: array:1 [
              0 => "list"
            ]
          }
          +parameters: TypeLang\Parser\Node\Stmt\Template\ParametersListNode {#267
            +offset: 77
            +list: array:1 [
              0 => TypeLang\Parser\Node\Stmt\Template\ParameterNode {#280
                +offset: 78
                +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#142
                  +offset: 78
                  +name: TypeLang\Parser\Node\Name {#165
                    +offset: 78
                    +name: "Some"
                    +isSimple: true
                    +parts: array:1 [
                      0 => "Some"
                    ]
                  }
                  +parameters: null
                  +arguments: null
                }
              }
            ]
          }
          +arguments: null
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#566
          +offset: 65
          +value: "field2"
        }
        +optional: false
      }
      2 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#574
        +offset: 93
        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#270
          +offset: 101
          +name: TypeLang\Parser\Node\Name {#115
            +offset: 101
            +name: "iterable"
            +isSimple: true
            +parts: array:1 [
              0 => "iterable"
            ]
          }
          +parameters: TypeLang\Parser\Node\Stmt\Template\ParametersListNode {#474
            +offset: 109
            +list: array:2 [
              0 => TypeLang\Parser\Node\Stmt\Template\ParameterNode {#120
                +offset: 110
                +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#172
                  +offset: 110
                  +name: TypeLang\Parser\Node\Name {#168
                    +offset: 110
                    +name: "array-key"
                    +isSimple: true
                    +parts: array:1 [
                      0 => "array-key"
                    ]
                  }
                  +parameters: null
                  +arguments: null
                }
              }
              1 => TypeLang\Parser\Node\Stmt\Template\ParameterNode {#125
                +offset: 121
                +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#171
                  +offset: 121
                  +name: TypeLang\Parser\Node\Name {#170
                    +offset: 121
                    +name: "array"
                    +isSimple: true
                    +parts: array:1 [
                      0 => "array"
                    ]
                  }
                  +parameters: null
                  +arguments: TypeLang\Parser\Node\Stmt\Shape\ArgumentsListNode {#157
                    +offset: 126
                    +list: array:2 [
                      0 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#174
                        +offset: 127
                        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#97
                          +offset: 127
                          +name: TypeLang\Parser\Node\Name {#337
                            +offset: 127
                            +name: "int"
                            +isSimple: true
                            +parts: array:1 [
                              0 => "int"
                            ]
                          }
                          +parameters: null
                          +arguments: null
                        }
                        +name: null
                        +optional: false
                      }
                      1 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#155
                        +offset: 132
                        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {#339
                          +offset: 132
                          +name: TypeLang\Parser\Node\Name {#341
                            +offset: 132
                            +name: "non-empty-string"
                            +isSimple: true
                            +parts: array:1 [
                              0 => "non-empty-string"
                            ]
                          }
                          +parameters: null
                          +arguments: null
                        }
                        +name: null
                        +optional: false
                      }
                    ]
                    +sealed: true
                  }
                }
              }
            ]
          }
          +arguments: null
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#545
          +offset: 93
          +value: "field3"
        }
        +optional: false
      }
      3 => TypeLang\Parser\Node\Stmt\Shape\ArgumentNode {#544
        +offset: 160
        +value: TypeLang\Parser\Node\Stmt\ClassConstMaskNode {#137
          +offset: 160
          +class: TypeLang\Parser\Node\Name {#139
            +offset: 160
            +name: "Some"
            +isSimple: true
            +parts: array:1 [
              0 => "Some"
            ]
          }
          +constant: "CONST_"
        }
        +name: null
        +optional: false
      }
    ]
    +sealed: false
  }
}
```
