<?php

declare(strict_types=1);

namespace {
    use Some\Any\Test1 as Example1;
    use Some\Any1;

    /**
     * @uses Any1
     * @uses Example1
     */
    final class Example {}
}

namespace TypeLang\Parser\Tests\TypeResolver\Stub {
    use Some\Any\Test2 as Example2;
    use Some\Any2;

    /**
     * @uses Any2
     * @uses Example2
     */
    final class MultipleNamespacesClassStub {}
}
