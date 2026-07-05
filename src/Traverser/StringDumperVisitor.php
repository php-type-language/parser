<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

final class StringDumperVisitor extends DumperVisitor
{
    public private(set) string $output = '';

    public function before(): void
    {
        $this->reset();

        parent::before();
    }

    public function reset(): void
    {
        $this->output = '';
    }

    protected function write(string $data): void
    {
        $this->output .= $data;
    }
}
