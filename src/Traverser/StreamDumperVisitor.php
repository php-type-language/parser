<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

final class StreamDumperVisitor extends DumperVisitor
{
    public const string DEFAULT_OUTPUT_STREAM = 'php://stderr';

    /**
     * @var resource
     */
    private readonly mixed $stream;

    public function __construct(
        string $simplifyNodeNamespace = self::DEFAULT_SIMPLIFIED_NODE_NAMESPACE,
        string $stream = self::DEFAULT_OUTPUT_STREAM,
    ) {
        parent::__construct($simplifyNodeNamespace);

        $resource = \fopen($stream, 'ab+');

        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException(\sprintf('Could not open "%s" for writing', $stream));
        }

        $this->stream = $resource;
    }

    protected function write(string $data): void
    {
        \fwrite($this->stream, $data);
    }
}
