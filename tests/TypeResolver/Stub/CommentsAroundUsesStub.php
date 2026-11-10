<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver\Stub;

use Some\First; // a trailing line comment
/* a block comment between statements */
use Some\Second as Aliased;
// a hash comment
use Some\Third;

/**
 * @uses First
 * @uses Aliased
 * @uses Third
 */
final class CommentsAroundUsesStub {}
