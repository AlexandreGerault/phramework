<?php

namespace AGerault\Framework\Contracts\Services;

use Psr\Container\ContainerInterface;

interface ServiceContainerInterface extends ContainerInterface
{
    /**
     * Register an alias (say which class to instantiate for a given interface)
     */
    public function addAlias(string $alias, string $target): void;

    /**
     * Register a service definition
     */
    public function register(string $id): void;

    public function getDefinition(string $id): ServiceDefinitionInterface;

    /**
     * Register a new parameter
     */
    public function addParameter(string $id, mixed $value): void;

    public function getParameter(string $id): mixed;
}
