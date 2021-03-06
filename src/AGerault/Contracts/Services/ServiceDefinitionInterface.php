<?php

namespace AGerault\Framework\Contracts\Services;

use AGerault\Framework\Services\ServiceContainer;

interface ServiceDefinitionInterface
{
    /**
     * Define if the service should be instanced once or each time it is called
     *
     * @return bool
     */
    public function isShared(): bool;

    /**
     * Give the alias of the service
     *
     * @return array
     */
    public function aliases(): array;

    /**
     * Give the definitions this depends on
     *
     * @return array<ServiceDefinitionInterface>
     */
    public function dependencies(): array;

    /**
     * Make the service be instantiated every time
     */
    public function makeNotShared(): void;

    /**
     * Make the service be instantiated only once (default)
     */
    public function makeShared(): void;

    /**
     * Build a new instance of the service
     *
     * @param ServiceContainer $container
     * @return object
     */
    public function newInstance(ServiceContainer $container): object;
}
