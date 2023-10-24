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
TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#737
  +offset: 0
  +name: TypeLang\Parser\Node\Name {#752
    +offset: 0
    +name: "array"
    +parts: array:1 [
      0 => "array"
    ]
  }
  +parameters: null
  +fields: TypeLang\Parser\Node\Stmt\Type\Shape\FieldsListNode {#723
    +offset: 5
    +list: array:5 [
      0 => TypeLang\Parser\Node\Stmt\Type\Shape\NamedFieldNode {#143
        +offset: 11
        +of: TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#364
          +offset: 19
          +value: TypeLang\Parser\Node\Stmt\Type\CallableTypeNode {#169
            +offset: 19
            +name: TypeLang\Parser\Node\Name {#175
              +offset: 19
              +name: "callable"
              +parts: array:1 [
                0 => "callable"
              ]
            }
            +arguments: TypeLang\Parser\Node\Stmt\Type\Callable\ArgumentsListNode {#174
              +offset: 28
              +list: array:2 [
                0 => TypeLang\Parser\Node\Stmt\Type\Callable\ArgumentNode {#616
                  +offset: 28
                  +type: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#648
                    +offset: 28
                    +name: TypeLang\Parser\Node\Name {#632
                      +offset: 28
                      +name: "Example"
                      +parts: array:1 [
                        0 => "Example"
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
                1 => TypeLang\Parser\Node\Stmt\Type\Callable\ArgumentNode {#646
                  +offset: 37
                  +type: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#622
                    +offset: 37
                    +name: TypeLang\Parser\Node\Name {#626
                      +offset: 37
                      +name: "int"
                      +parts: array:1 [
                        0 => "int"
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
              ]
            }
            +type: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#649
              +offset: 42
              +name: TypeLang\Parser\Node\Name {#631
                +offset: 42
                +name: "mixed"
                +parts: array:1 [
                  0 => "mixed"
                ]
              }
              +parameters: null
              +fields: null
            }
          }
        }
        +name: TypeLang\Parser\Node\Stmt\Literal\StringLiteralNode {#167
          +offset: 11
          +raw: "field1"
          +value: "field1"
        }
      }
      1 => TypeLang\Parser\Node\Stmt\Type\Shape\NamedFieldNode {#355
        +offset: 53
        +of: TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#154
          +offset: 61
          +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#658
            +offset: 61
            +name: TypeLang\Parser\Node\Name {#475
              +offset: 61
              +name: "list"
              +parts: array:1 [
                0 => "list"
              ]
            }
            +parameters: TypeLang\Parser\Node\Stmt\Type\Template\ParametersListNode {#484
              +offset: 65
              +list: array:1 [
                0 => TypeLang\Parser\Node\Stmt\Type\Template\ParameterNode {#653
                  +offset: 66
                  +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#618
                    +offset: 66
                    +name: TypeLang\Parser\Node\Name {#605
                      +offset: 66
                      +name: "Some"
                      +parts: array:1 [
                        0 => "Some"
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
        +name: TypeLang\Parser\Node\Stmt\Literal\StringLiteralNode {#153
          +offset: 53
          +raw: "field2"
          +value: "field2"
        }
      }
      2 => TypeLang\Parser\Node\Stmt\Type\Shape\NamedFieldNode {#149
        +offset: 77
        +of: TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#170
          +offset: 85
          +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#613
            +offset: 85
            +name: TypeLang\Parser\Node\Name {#623
              +offset: 85
              +name: "iterable"
              +parts: array:1 [
                0 => "iterable"
              ]
            }
            +parameters: TypeLang\Parser\Node\Stmt\Type\Template\ParametersListNode {#609
              +offset: 93
              +list: array:2 [
                0 => TypeLang\Parser\Node\Stmt\Type\Template\ParameterNode {#621
                  +offset: 94
                  +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#606
                    +offset: 94
                    +name: TypeLang\Parser\Node\Name {#593
                      +offset: 94
                      +name: "array-key"
                      +parts: array:1 [
                        0 => "array-key"
                      ]
                    }
                    +parameters: null
                    +fields: null
                  }
                }
                1 => TypeLang\Parser\Node\Stmt\Type\Template\ParameterNode {#592
                  +offset: 105
                  +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#546
                    +offset: 105
                    +name: TypeLang\Parser\Node\Name {#589
                      +offset: 105
                      +name: "array"
                      +parts: array:1 [
                        0 => "array"
                      ]
                    }
                    +parameters: null
                    +fields: TypeLang\Parser\Node\Stmt\Type\Shape\FieldsListNode {#591
                      +offset: 110
                      +list: array:2 [
                        0 => TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#601
                          +offset: 111
                          +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#98
                            +offset: 111
                            +name: TypeLang\Parser\Node\Name {#332
                              +offset: 111
                              +name: "int"
                              +parts: array:1 [
                                0 => "int"
                              ]
                            }
                            +parameters: null
                            +fields: null
                          }
                        }
                        1 => TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#597
                          +offset: 116
                          +value: TypeLang\Parser\Node\Stmt\Type\NamedTypeNode {#336
                            +offset: 116
                            +name: TypeLang\Parser\Node\Name {#337
                              +offset: 116
                              +name: "non-empty-string"
                              +parts: array:1 [
                                0 => "non-empty-string"
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
        +name: TypeLang\Parser\Node\Stmt\Literal\StringLiteralNode {#160
          +offset: 77
          +raw: "field3"
          +value: "field3"
        }
      }
      3 => TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#171
        +offset: 140
        +value: TypeLang\Parser\Node\Stmt\Type\ClassConstMaskNode {#603
          +offset: 140
          +class: TypeLang\Parser\Node\Name {#615
            +offset: 140
            +name: "Some"
            +parts: array:1 [
              0 => "Some"
            ]
          }
          +constant: "CONST_"
        }
      }
      4 => TypeLang\Parser\Node\Stmt\Type\Shape\NamedFieldNode {#163
        +offset: 159
        +of: TypeLang\Parser\Node\Stmt\Type\Shape\FieldNode {#162
          +offset: 175
          +value: TypeLang\Parser\Node\Stmt\Type\ConstMaskNode {#596
            +offset: 175
            +name: TypeLang\Parser\Node\FullQualifiedName {#583
              +offset: 175
              +name: "JSON_"
              +parts: array:1 [
                0 => "JSON_"
              ]
            }
          }
        }
        +name: TypeLang\Parser\Node\Stmt\Literal\StringLiteralNode {#161
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
