<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

enum NamespaceType
{
    case SEMICOLON;
    case BRACED;
}
