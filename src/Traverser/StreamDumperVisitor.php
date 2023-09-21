<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

final class StreamDumperVisitor extends DumperVisitor
{
    /**
     * @var resource
     */
    private readonly mixed $stream;

    public function __construct(
        bool $simplifyNames = true,
        string $stream = 'php://stderr',
    ) {
        parent::__construct($simplifyNames);

        $this->stream = \fopen($stream, 'ab+');
    }

    protected function write(string $data): void
    {
        \fwrite($this->stream, $data);
    }
}
