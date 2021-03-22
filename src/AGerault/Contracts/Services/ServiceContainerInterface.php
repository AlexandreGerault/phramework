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
}
