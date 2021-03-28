<?php

namespace AGerault\Framework\Contracts\Services;

use Psr\Container\ContainerInterface;

interface ServiceContainerInterface extends ContainerInterface
{
    /**
     * Register an alias (say which class to instantiate for a given interface)
     *
     * @param string $alias
     * @param string $target
     */
    public function addAlias(string $alias, string $target): void;

    /**
     * Register a service definition
     *
     * @param string $id
     */
    public function register(string $id): void;

    /**
     * @param string $id
     * @return ServiceDefinitionInterface
     */
    public function getDefinition(string $id): ServiceDefinitionInterface;

    /**
     * Register a new parameter
     *
     * @param string $id
     * @param mixed $value
     */
    public function addParameter(string $id, mixed $value): void;

    /**
     * @param string $id
     * @return mixed
     */
    public function getParameter(string $id): mixed;
}
