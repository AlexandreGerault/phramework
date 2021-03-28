<?php

namespace Test\Fixtures\Services;

class UnionTypeServiceInjection
{
    protected FooService | FooAltService $service;

    /**
     * UnionTypeServiceInjection constructor.
     *
     * @param FooAltService|FooService $service
     */
    public function __construct(FooService | FooAltService $service)
    {
        $this->service = $service;
    }
}
