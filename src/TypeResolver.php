<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Parser\TypeResolver\PhpUseStatementsTransformer;
use TypeLang\Type\TypeNode;

final readonly class TypeResolver
{
    public function __construct(
        /**
         * @var array<array-key, non-empty-string>
         */
        private array $imports = [],
    ) {}

    /**
     * Registers a non-aliased `use Some\Any;` import, so that every relative
     * name starting with the imported one is expanded to its full form.
     *
     * For example, for such code:
     * ```
     * use TypeLang\Parser\Node;
     * ```
     *
     * You need to add an import like this:
     * ```
     * $resolver = new TypeResolver()
     *     ->withTypeImport('TypeLang\Parser\Node');
     *
     * $ast = new TypeParser()
     *     ->parse('Node\SemanticException');
     *
     * $resolver->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Parser\Node\SemanticException
     * echo $ast->name->toString();
     * ```
     *
     * @api
     * @param non-empty-string $name
     */
    public function withTypeImport(string $name): self
    {
        return new self([...$this->imports, $name]);
    }

    /**
     * Registers an aliased `use Some\Any as AliasName;` import, so that every
     * relative name starting with the alias is expanded to the imported type.
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
     *     ->parse('Error\SemanticException');
     *
     * $resolver->resolve($ast);
     *
     * // Expected Output:
     * // > TypeLang\Parser\Exception\SemanticException
     * echo $ast->name->toString();
     * ```
     *
     * @api
     * @param non-empty-string $name
     * @param non-empty-string $alias
     */
    public function withTypeImportAs(string $name, string $alias): self
    {
        return new self([...$this->imports, $alias => $name]);
    }

    private function createTraverser(): TraverserInterface
    {
        $transformer = new PhpUseStatementsTransformer($this->imports);

        return new Traverser([
            new TypeMapVisitor($transformer(...)),
        ]);
    }

    /**
     * Rewrites every type name in the given AST according to the registered
     * imports, mutating and returning the same node instance.
     *
     * ```
     * $ast = new TypeParser()
     *     ->parse(<<<'PHP'
     *         array { Node, Error\SemanticException }
     *         PHP);
     *
     * new TypeResolver()
     *     ->withTypeImport('TypeLang\Parser\Node')
     *     ->withTypeImportAs('TypeLang\Parser\Exception', 'Error')
     *     ->resolve($ast);
     *
     * // Expected Output:
     * // > array{
     * // >     TypeLang\Parser\Node,
     * // >     TypeLang\Parser\Exception\SemanticException
     * // > }
     * ```
     *
     * @api
     */
    public function resolve(TypeNode $type): TypeNode
    {
        $traverser = $this->createTraverser();
        $traverser->traverse([$type]);

        return $type;
    }
}
