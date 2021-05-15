<?php


namespace Test\Fixtures\Services;


class DependsOnFactoryService
{
    private FactoryService $factory;

    /**
     * DependsOnFactoryService constructor.
     */
    public function __construct(FactoryService $factory)
    {
        $this->factory = $factory;
    }

    public function factory(): FactoryService
    {
        return $this->factory;
    }
}
