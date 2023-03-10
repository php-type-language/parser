<a href="https://github.com/php-type-language" target="_blank">
    <picture>
        <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
    </picture>
</a>

The PHP reference implementation for Type Language.

<p align="center">
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/phptl/parser"><img src="https://poser.pugx.org/phptl/parser/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/parser/blob/master/LICENSE"><img src="https://poser.pugx.org/phptl/parser/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/parser/actions"><img src="https://github.com/php-type-language/parser/workflows/main/badge.svg"></a>
</p>

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
array:1 [
  0 => TypeLang\Parser\Node\Stmt\NamedTypeStmt {#562
    +offset: 0
    +name: TypeLang\Parser\Node\Name {#561
      +offset: 0
      +name: "array"
      +isSimple: true
      +parts: array:1 [
        0 => "array"
      ]
    }
    +parameters: null
    +arguments: TypeLang\Parser\Node\Stmt\Shape\Arguments {#545
      +offset: 5
      +list: array:4 [
        0 => TypeLang\Parser\Node\Stmt\Shape\Argument {#552
          +offset: 15
          +value: TypeLang\Parser\Node\Stmt\CallableTypeStmt {#235
            +offset: 23
            +name: TypeLang\Parser\Node\Name {#133
              +offset: 23
              +name: "callable"
              +isSimple: true
              +parts: array:1 [
                0 => "callable"
              ]
            }
            +arguments: TypeLang\Parser\Node\Stmt\Callable\Arguments {#131
              +offset: 32
              +list: array:2 [
                0 => TypeLang\Parser\Node\Stmt\Callable\Argument {#146
                  +offset: 32
                  +type: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#443
                    +offset: 32
                    +name: TypeLang\Parser\Node\Name {#480
                      +offset: 32
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
                1 => TypeLang\Parser\Node\Stmt\Callable\Argument {#145
                  +offset: 41
                  +type: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#351
                    +offset: 41
                    +name: TypeLang\Parser\Node\Name {#343
                      +offset: 41
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
            +type: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#522
              +offset: 46
              +name: TypeLang\Parser\Node\Name {#491
                +offset: 46
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
          +name: TypeLang\Parser\Node\Literal\StringLiteralStmt {#547
            +offset: 15
            +value: "field1"
          }
          +optional: false
        }
        1 => TypeLang\Parser\Node\Stmt\Shape\Argument {#560
          +offset: 61
          +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#147
            +offset: 69
            +name: TypeLang\Parser\Node\Name {#136
              +offset: 69
              +name: "list"
              +isSimple: true
              +parts: array:1 [
                0 => "list"
              ]
            }
            +parameters: TypeLang\Parser\Node\Stmt\Template\Parameters {#150
              +offset: 73
              +list: array:1 [
                0 => TypeLang\Parser\Node\Stmt\Template\Parameter {#141
                  +offset: 74
                  +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#492
                    +offset: 74
                    +name: TypeLang\Parser\Node\Name {#475
                      +offset: 74
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
          +name: TypeLang\Parser\Node\Literal\StringLiteralStmt {#543
            +offset: 61
            +value: "field2"
          }
          +optional: false
        }
        2 => TypeLang\Parser\Node\Stmt\Shape\Argument {#546
          +offset: 89
          +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#137
            +offset: 97
            +name: TypeLang\Parser\Node\Name {#517
              +offset: 97
              +name: "iterable"
              +isSimple: true
              +parts: array:1 [
                0 => "iterable"
              ]
            }
            +parameters: TypeLang\Parser\Node\Stmt\Template\Parameters {#497
              +offset: 105
              +list: array:2 [
                0 => TypeLang\Parser\Node\Stmt\Template\Parameter {#513
                  +offset: 106
                  +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#462
                    +offset: 106
                    +name: TypeLang\Parser\Node\Name {#482
                      +offset: 106
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
                1 => TypeLang\Parser\Node\Stmt\Template\Parameter {#490
                  +offset: 117
                  +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#463
                    +offset: 117
                    +name: TypeLang\Parser\Node\Name {#458
                      +offset: 117
                      +name: "array"
                      +isSimple: true
                      +parts: array:1 [
                        0 => "array"
                      ]
                    }
                    +parameters: null
                    +arguments: TypeLang\Parser\Node\Stmt\Shape\Arguments {#470
                      +offset: 122
                      +list: array:2 [
                        0 => TypeLang\Parser\Node\Stmt\Shape\Argument {#461
                          +offset: 123
                          +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#350
                            +offset: 123
                            +name: TypeLang\Parser\Node\Name {#97
                              +offset: 123
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
                        1 => TypeLang\Parser\Node\Stmt\Shape\Argument {#478
                          +offset: 128
                          +value: TypeLang\Parser\Node\Stmt\NamedTypeStmt {#217
                            +offset: 128
                            +name: TypeLang\Parser\Node\Name {#218
                              +offset: 128
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
          +name: TypeLang\Parser\Node\Literal\StringLiteralStmt {#122
            +offset: 89
            +value: "field3"
          }
          +optional: false
        }
        3 => TypeLang\Parser\Node\Stmt\Shape\Argument {#126
          +offset: 156
          +value: TypeLang\Parser\Node\Stmt\ClassConstMaskStmt {#512
            +offset: 156
            +class: TypeLang\Parser\Node\Name {#469
              +offset: 156
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
]
```
