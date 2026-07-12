<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver\PhpUseStatementsReader;

final readonly class ReflectionSourcePrefixReader
{
    /**
     * @param \ReflectionClass<object> $class
     */
    public function readClassHeader(\ReflectionClass $class): string
    {
        /** @var non-empty-string|false $pathname */
        $pathname = $class->getFileName();

        if ($pathname === false || !$class->isUserDefined()) {
            return '';
        }

        /** @var int<1, max>|false $startsAt */
        $startsAt = $class->getStartLine();

        if ($startsAt === false) {
            return '';
        }

        return $this->read($pathname, $startsAt);
    }

    public function readFunctionHeader(\ReflectionFunctionAbstract $function): string
    {
        /** @var non-empty-string|false $pathname */
        $pathname = $function->getFileName();

        if ($pathname === false || !$function->isUserDefined()) {
            return '';
        }

        /** @var int<1, max>|false $startsAt */
        $startsAt = $function->getStartLine();

        if ($startsAt === false) {
            return '';
        }

        return $this->read($pathname, $startsAt);
    }

    /**
     * Read file source up to the line where our entry is defined.
     *
     * @param non-empty-string $pathname
     * @param int<0, max> $startsAt
     * @throws \LogicException
     * @throws \RuntimeException
     */
    private function read(string $pathname, int $startsAt): string
    {
        $source = new \SplFileObject($pathname);
        $source->flock(\LOCK_SH);

        $line = 0;
        $result = '';

        while (!$source->eof()) {
            if (++$line >= $startsAt) {
                break;
            }

            $result .= $source->fgets();
        }

        $source->flock(\LOCK_UN);
        unset($source);

        return $result;
    }
}
