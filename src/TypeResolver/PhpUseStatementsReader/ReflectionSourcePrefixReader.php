<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver\PhpUseStatementsReader;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\TypeResolver
 */
final class ReflectionSourcePrefixReader
{
    /**
     * @param \ReflectionClass<object> $class
     * @throws \LogicException in case of the file of the class is a directory
     * @throws \RuntimeException in case of the file of the class cannot be
     *         opened
     */
    public function readClassHeader(\ReflectionClass $class): string
    {
        /** @var non-empty-string|false */
        $pathname = $class->getFileName();

        if ($pathname === false || !$class->isUserDefined()) {
            return '';
        }

        /** @var int<1, max>|false */
        $startsAt = $class->getStartLine();

        if ($startsAt === false) {
            return '';
        }

        return $this->read($pathname, $startsAt);
    }

    /**
     * @throws \LogicException in case of the file of the function is a
     *         directory
     * @throws \RuntimeException in case of the file of the function cannot be
     *         opened
     */
    public function readFunctionHeader(\ReflectionFunctionAbstract $function): string
    {
        /** @var non-empty-string|false */
        $pathname = $function->getFileName();

        if ($pathname === false || !$function->isUserDefined()) {
            return '';
        }

        /** @var int<1, max>|false */
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
     * @throws \LogicException in case of the file is a directory
     * @throws \RuntimeException in case of the file cannot be opened
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
