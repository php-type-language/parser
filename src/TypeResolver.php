<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Parser\TypeResolver\PhpUseStatementsReader;
use TypeLang\Parser\TypeResolver\PhpUseStatementsTransformer;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\TypeNode;

/**
 * Resolves the names of a type as PHP resolves the names of classes in the
 * place the type is written: by the imports, by the namespace and by the
 * class for `self`, `static` and `parent`. What is not registered is kept
 * as written.
 *
 * ```
 * namespace App;
 *
 * use Other\Status;
 *
 * final class User {}
 *
 * // `Status` to `Other\Status` by the import
 * // `Address` to `App\Address` by the namespace
 * // `self::TYPE_*` to `App\User::TYPE_*` by the class
 * ```
 */
final class TypeResolver
{
    /**
     * Created on the first resolved type
     */
    private ?TraverserInterface $traverser = null;

    public function __construct(
        /**
         * @var array<array-key, non-empty-string>
         */
        private readonly array $imports = [],
        /**
         * Namespace of the relative names, an empty string for the global
         * one, {@see null} in case of the relative names are kept as written.
         */
        private readonly ?string $namespace = null,
        /**
         * Returns whether the relative name in the namespace is a class, a
         * name it rejects is kept as written. {@see null} in case of a
         * class, an interface or an enum of the name exists.
         *
         * @var (\Closure(non-empty-string): bool)|null
         */
        private readonly ?\Closure $exists = null,
        /**
         * Class of `self`.
         *
         * @var class-string|null
         */
        private readonly ?string $self = null,
        /**
         * Class of `static`.
         *
         * @var class-string|null
         */
        private readonly ?string $static = null,
    ) {}

    /**
     * Registers a non-aliased `use Some\Any;` import: every relative name
     * starting with the imported one is expanded to its full form.
     *
     * For example, for such code:
     * ```
     * use TypeLang\Type\Shape;
     * ```
     *
     * You need to add an import like this:
     * ```
     * $resolver = new TypeResolver()
     *     ->withTypeImport('TypeLang\Type\Shape');
     *
     * $ast = new TypeParser()
     *     ->parse('Shape\FieldListNode');
     *
     * $resolver->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Type\Shape\FieldListNode
     * echo $ast->name->toString();
     * ```
     *
     * @param non-empty-string $name
     */
    public function withTypeImport(string $name): self
    {
        return $this->withImports([...$this->imports, $name]);
    }

    /**
     * Registers an aliased `use Some\Any as AliasName;` import: every relative
     * name starting with the alias is expanded to the imported type.
     *
     * For example, for such code:
     * ```
     * use TypeLang\Parser\Exception as Error;
     * ```
     *
     * You need to add an import like this:
     * ```
     * $resolver = new TypeResolver()
     *     ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');
     *
     * $ast = new TypeParser()
     *     ->parse('Error\ParsingException');
     *
     * $resolver->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Parser\Exception\ParsingException
     * echo $ast->name->toString();
     * ```
     *
     * @param non-empty-string $name
     * @param non-empty-string $alias
     */
    public function withTypeImportAs(string $name, string $alias): self
    {
        return $this->withImports([...$this->imports, $alias => $name]);
    }

    /**
     * Registers every `use` import declared in the source file of the given
     * class. The namespace and `self` of the class are registered separately,
     * by {@see TypeResolver::withNamespace()} and
     * {@see TypeResolver::withClasses()}.
     *
     * For a class whose file declares:
     * ```
     * namespace App;
     *
     * use TypeLang\Type\Name;
     * use TypeLang\Parser\Exception as Error;
     *
     * final class Example {}
     * ```
     *
     * You can reuse its imports like this:
     * ```
     * $ast = new TypeParser()
     *     ->parse(<<<'PHP'
     *         Error\ParsingException
     *         PHP);
     *
     * $ast = new TypeResolver()
     *     ->withTypeImportsFromClass(new \ReflectionClass(App\Example::class))
     *     ->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Parser\Exception\ParsingException
     * echo $ast->name->toString();
     * ```
     *
     * @param \ReflectionClass<object> $class
     */
    public function withTypeImportsFromClass(\ReflectionClass $class): self
    {
        $reader = new PhpUseStatementsReader();
        $statements = $reader->getClassUseStatements($class);

        return $this->withImports([...$this->imports, ...$statements]);
    }

    /**
     * Registers every `use` import available in the source file of the given
     * function or method. The namespace is registered separately, by
     * {@see TypeResolver::withNamespace()}.
     *
     * For a function whose file declares:
     * ```
     * namespace App;
     *
     * use TypeLang\Type\Name;
     * use TypeLang\Parser\Exception as Error;
     *
     * function example(): void {}
     * ```
     *
     * You can reuse its imports like this:
     * ```
     * $ast = new TypeParser()
     *     ->parse(<<<'PHP'
     *         Error\ParsingException
     *         PHP);
     *
     * $ast = new TypeResolver()
     *     ->withTypeImportsFromFunction(new \ReflectionFunction('App\example'))
     *     ->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Parser\Exception\ParsingException
     * echo $ast->name->toString();
     * ```
     */
    public function withTypeImportsFromFunction(\ReflectionFunctionAbstract $function): self
    {
        // A method takes the imports of its declaring class
        if ($function instanceof \ReflectionMethod) {
            return $this->withTypeImportsFromClass($function->getDeclaringClass());
        }

        $reader = new PhpUseStatementsReader();
        $statements = $reader->getFunctionUseStatements($function);

        return $this->withImports([...$this->imports, ...$statements]);
    }

    /**
     * Registers the namespace the type is written in: a relative name that is
     * not imported belongs to it in case of the check accepts the name in the
     * namespace, `namespace\Name` always does. An empty string is the global
     * namespace.
     *
     * The check keeps the names of types that are not classes, such as
     * `list`, `positive-int` or a type alias: by default a name is resolved
     * in case of a class, an interface or an enum of it exists. A check that
     * accepts any name resolves as PHP does.
     *
     * ```
     * $resolver = new TypeResolver()
     *     ->withNamespace('App');
     *
     * // `Address` to `App\Address` in case of the class exists
     * // `list<int>` as is
     * // `namespace\Address` to `App\Address` always
     * ```
     *
     * @param (\Closure(non-empty-string): bool)|null $exists returns whether
     *        the name in the namespace is a class, {@see null} in case of the
     *        default check
     */
    public function withNamespace(string $namespace, ?\Closure $exists = null): self
    {
        return new self(
            imports: $this->imports,
            namespace: \trim($namespace, '\\'),
            exists: $exists,
            self: $this->self,
            static: $this->static,
        );
    }

    /**
     * Registers the classes of `self`, `parent` and `static`: `self` is the
     * class the type is written in, `parent` is its parent, `static` is the
     * class the type is used for, `self` by default.
     *
     * ```
     * $resolver = new TypeResolver()
     *     ->withClasses(App\User::class);
     *
     * // `self::TYPE_*` to `App\User::TYPE_*`
     * ```
     *
     * @param class-string $self
     * @param class-string|null $static
     */
    public function withClasses(string $self, ?string $static = null): self
    {
        return new self(
            imports: $this->imports,
            namespace: $this->namespace,
            exists: $this->exists,
            self: $self,
            static: $static ?? $self,
        );
    }

    /**
     * Rewrites every type name in the given AST according to the registered
     * imports, namespace and classes, mutating and returning the same node
     * instance.
     *
     * ```
     * $ast = new TypeParser()
     *     ->parse(<<<'PHP'
     *         array { Name, Error\ParsingException }
     *         PHP);
     *
     * new TypeResolver()
     *     ->withTypeImport('TypeLang\Type\Name')
     *     ->withTypeImportAs('TypeLang\Parser\Exception', 'Error')
     *     ->resolve($ast);
     *
     * // Expected Output:
     * // > array{
     * // >     TypeLang\Type\Name,
     * // >     TypeLang\Parser\Exception\ParsingException
     * // > }
     * ```
     *
     * @throws \UnexpectedValueException in case of `parent` is written in a
     *         class without a parent
     * @throws \InvalidArgumentException in case of an import has no name
     *         segment
     */
    public function resolve(TypeNode $type): TypeNode
    {
        $this->traverser ??= $this->createTraverser();
        $this->traverser->traverse([$type]);

        return $type;
    }

    /**
     * @param array<array-key, non-empty-string> $imports
     */
    private function withImports(array $imports): self
    {
        return new self(
            imports: $imports,
            namespace: $this->namespace,
            exists: $this->exists,
            self: $this->self,
            static: $this->static,
        );
    }

    /**
     * @throws \InvalidArgumentException in case of an import has no name
     *         segment
     */
    private function createTraverser(): TraverserInterface
    {
        $imports = new PhpUseStatementsTransformer($this->imports);

        return new Traverser([
            new TypeMapVisitor(fn(Name $name): ?Name => $this->resolveName($name, $imports)),
        ]);
    }

    /**
     * Returns {@see null} in case of the name is kept as written.
     *
     * @throws \UnexpectedValueException in case of `parent` is written in a
     *         class without a parent
     */
    private function resolveName(Name $name, PhpUseStatementsTransformer $imports): ?Name
    {
        if ($name->isSpecial()) {
            return $this->resolveSpecialName($name);
        }

        if ($this->namespace !== null && $name->namespace?->kind === NamespaceKind::NamespaceRelative) {
            return self::createName([...$this->getNamespaceWords(), ...$name->toArray()], $name);
        }

        $imported = $imports($name);

        if ($imported !== null) {
            return $imported;
        }

        if ($this->namespace === null || $this->namespace === '') {
            return null;
        }

        if ($name->isFullyQualified() || $name->isBuiltin()) {
            return null;
        }

        $words = [...$this->getNamespaceWords(), ...$name->toArray()];

        $resolved = \implode('\\', $words);

        if (!$this->isClass($resolved)) {
            return null;
        }

        return self::createName($words, $name);
    }

    /**
     * @throws \UnexpectedValueException in case of `parent` is written in a
     *         class without a parent
     */
    private function resolveSpecialName(Name $name): ?Name
    {
        if ($this->self === null || $this->static === null) {
            return null;
        }

        $class = match ($name->toLowerString()) {
            'static' => $this->static,
            'parent' => \get_parent_class($this->self),
            default => $this->self,
        };

        if ($class === false) {
            throw new \UnexpectedValueException(\sprintf(
                'class "%s" has no parent, "parent" cannot be resolved',
                \addcslashes($this->self, '"'),
            ));
        }

        /** @var list<non-empty-string> */
        $words = \explode('\\', $class);

        return self::createName($words, $name);
    }

    /**
     * @param non-empty-string $name
     */
    private function isClass(string $name): bool
    {
        if ($this->exists !== null) {
            return ($this->exists)($name);
        }

        return \class_exists($name) || \interface_exists($name) || \enum_exists($name);
    }

    /**
     * @return list<non-empty-string>
     */
    private function getNamespaceWords(): array
    {
        if ($this->namespace === null || $this->namespace === '') {
            return [];
        }

        /** @var list<non-empty-string> */
        return \explode('\\', $this->namespace);
    }

    /**
     * @param list<non-empty-string> $words
     */
    private static function createName(array $words, Name $written): Name
    {
        $last = \array_pop($words);

        \assert($last !== null);

        if ($words === []) {
            return new Name($last, null, $written->offset);
        }

        return new Name($last, new NamespaceName(\implode('\\', $words)), $written->offset);
    }
}
