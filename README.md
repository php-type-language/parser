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
        "\njson_flags": \JSON_*,
        ...
    }
    PHP);
```

Expected Output:

```php
TypeLang\Parser\Node\Type\NamedTypeNode {#756
  +offset: 0
  +name: TypeLang\Parser\Node\Name {#778
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {#771
        +offset: 0
        +value: "array"
      }
    ]
  }
  +parameters: null
  +fields: TypeLang\Parser\Node\Type\Shape\FieldsListNode {#742
    +offset: 5
    +list: array:5 [
      0 => TypeLang\Parser\Node\Type\Shape\NamedFieldNode {#153
        +offset: 11
        +of: TypeLang\Parser\Node\Type\Shape\FieldNode {#356
          +offset: 19
          +value: TypeLang\Parser\Node\Type\CallableTypeNode {#167
            +offset: 19
            +name: TypeLang\Parser\Node\Name {#166
              +offset: 19
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {#179
                  +offset: 19
                  +value: "callable"
                }
              ]
            }
            +arguments: TypeLang\Parser\Node\Type\Callable\ArgumentsListNode {#170
              +offset: 28
              +list: array:2 [
                0 => TypeLang\Parser\Node\Type\Callable\ArgumentNode {#640
                  +offset: 28
                  +type: TypeLang\Parser\Node\Type\NamedTypeNode {#606
                    +offset: 28
                    +name: TypeLang\Parser\Node\Name {#616
                      +offset: 28
                      -parts: array:1 [
                        0 => TypeLang\Parser\Node\Identifier {#607
                          +offset: 28
                          +value: "Example"
                        }
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
                1 => TypeLang\Parser\Node\Type\Callable\ArgumentNode {#484
                  +offset: 37
                  +type: TypeLang\Parser\Node\Type\NamedTypeNode {#603
                    +offset: 37
                    +name: TypeLang\Parser\Node\Name {#602
                      +offset: 37
                      -parts: array:1 [
                        0 => TypeLang\Parser\Node\Identifier {#615
                          +offset: 37
                          +value: "int"
                        }
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
              ]
            }
            +type: TypeLang\Parser\Node\Type\NamedTypeNode {#626
              +offset: 42
              +name: TypeLang\Parser\Node\Name {#617
                +offset: 42
                -parts: array:1 [
                  0 => TypeLang\Parser\Node\Identifier {#625
                    +offset: 42
                    +value: "mixed"
                  }
                ]
              }
              +parameters: null
              +fields: null
            }
          }
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#364
          +offset: 11
          +raw: "field1"
          +value: "field1"
        }
      }
      1 => TypeLang\Parser\Node\Type\Shape\NamedFieldNode {#154
        +offset: 53
        +of: TypeLang\Parser\Node\Type\Shape\FieldNode {#168
          +offset: 61
          +value: TypeLang\Parser\Node\Type\NamedTypeNode {#475
            +offset: 61
            +name: TypeLang\Parser\Node\Name {#647
              +offset: 61
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {#483
                  +offset: 61
                  +value: "list"
                }
              ]
            }
            +parameters: TypeLang\Parser\Node\Type\Template\ParametersListNode {#627
              +offset: 65
              +list: array:1 [
                0 => TypeLang\Parser\Node\Type\Template\ParameterNode {#642
                  +offset: 66
                  +value: TypeLang\Parser\Node\Type\NamedTypeNode {#605
                    +offset: 66
                    +name: TypeLang\Parser\Node\Name {#579
                      +offset: 66
                      -parts: array:1 [
                        0 => TypeLang\Parser\Node\Identifier {#590
                          +offset: 66
                          +value: "Some"
                        }
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
              ]
            }
            +fields: null
          }
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#159
          +offset: 53
          +raw: "field2"
          +value: "field2"
        }
      }
      2 => TypeLang\Parser\Node\Type\Shape\NamedFieldNode {#158
        +offset: 77
        +of: TypeLang\Parser\Node\Type\Shape\FieldNode {#176
          +offset: 85
          +value: TypeLang\Parser\Node\Type\NamedTypeNode {#612
            +offset: 85
            +name: TypeLang\Parser\Node\Name {#599
              +offset: 85
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {#597
                  +offset: 85
                  +value: "iterable"
                }
              ]
            }
            +parameters: TypeLang\Parser\Node\Type\Template\ParametersListNode {#586
              +offset: 93
              +list: array:2 [
                0 => TypeLang\Parser\Node\Type\Template\ParameterNode {#592
                  +offset: 94
                  +value: TypeLang\Parser\Node\Type\NamedTypeNode {#585
                    +offset: 94
                    +name: TypeLang\Parser\Node\Name {#552
                      +offset: 94
                      -parts: array:1 [
                        0 => TypeLang\Parser\Node\Identifier {#578
                          +offset: 94
                          +value: "array-key"
                        }
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
                1 => TypeLang\Parser\Node\Type\Template\ParameterNode {#600
                  +offset: 105
                  +value: TypeLang\Parser\Node\Type\NamedTypeNode {#576
                    +offset: 105
                    +name: TypeLang\Parser\Node\Name {#594
                      +offset: 105
                      -parts: array:1 [
                        0 => TypeLang\Parser\Node\Identifier {#595
                          +offset: 105
                          +value: "array"
                        }
                      ]
                    }
                    +parameters: null
                    +fields: TypeLang\Parser\Node\Type\Shape\FieldsListNode {#593
                      +offset: 110
                      +list: array:2 [
                        0 => TypeLang\Parser\Node\Type\Shape\FieldNode {#482
                          +offset: 111
                          +value: TypeLang\Parser\Node\Type\NamedTypeNode {#341
                            +offset: 111
                            +name: TypeLang\Parser\Node\Name {#343
                              +offset: 111
                              -parts: array:1 [
                                0 => TypeLang\Parser\Node\Identifier {#342
                                  +offset: 111
                                  +value: "int"
                                }
                              ]
                            }
                            +parameters: null
                            +fields: null
                          }
                        }
                        1 => TypeLang\Parser\Node\Type\Shape\FieldNode {#333
                          +offset: 116
                          +value: TypeLang\Parser\Node\Type\NamedTypeNode {#347
                            +offset: 116
                            +name: TypeLang\Parser\Node\Name {#349
                              +offset: 116
                              -parts: array:1 [
                                0 => TypeLang\Parser\Node\Identifier {#348
                                  +offset: 116
                                  +value: "non-empty-string"
                                }
                              ]
                            }
                            +parameters: null
                            +fields: null
                          }
                        }
                      ]
                      +sealed: true
                    }
                  }
                }
              ]
            }
            +fields: null
          }
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#175
          +offset: 77
          +raw: "field3"
          +value: "field3"
        }
      }
      3 => TypeLang\Parser\Node\Type\Shape\FieldNode {#164
        +offset: 140
        +value: TypeLang\Parser\Node\Type\ClassConstMaskNode {#587
          +offset: 140
          +class: TypeLang\Parser\Node\Name {#580
            +offset: 140
            -parts: array:1 [
              0 => TypeLang\Parser\Node\Identifier {#577
                +offset: 140
                +value: "Some"
              }
            ]
          }
          +constant: TypeLang\Parser\Node\Identifier {#583
            +offset: 146
            +value: "CONST_"
          }
        }
      }
      4 => TypeLang\Parser\Node\Type\Shape\NamedFieldNode {#165
        +offset: 159
        +of: TypeLang\Parser\Node\Type\Shape\FieldNode {#180
          +offset: 175
          +value: TypeLang\Parser\Node\Type\ConstMaskNode {#574
            +offset: 175
            +name: TypeLang\Parser\Node\FullQualifiedName {#575
              +offset: 175
              -parts: array:1 [
                0 => TypeLang\Parser\Node\Identifier {#591
                  +offset: 176
                  +value: "JSON_"
                }
              ]
            }
          }
        }
        +name: TypeLang\Parser\Node\Literal\StringLiteralNode {#610
          +offset: 159
          +raw: ""\njson_flags""
          +value: """
            \n
            json_flags
            """
        }
      }
    ]
    +sealed: false
  }
}
```
