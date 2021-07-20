<?php

namespace Test\Fixtures\Services;

class Factory
{
    public static function makeFactoryService(string $name): FactoryService
    {
        return new FactoryService($name);
    }
}
