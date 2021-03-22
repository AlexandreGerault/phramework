<?php


namespace Test\Fixtures\Services;


class BarService
{
    protected FooService $foo;

    public function __construct(FooService $foo)
    {
        $this->foo = $foo;
    }
}
