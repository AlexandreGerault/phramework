<?php

namespace AGerault\Framework\Services;

use AGerault\Framework\Contracts\Services\ServiceDefinitionInterface;

class ServiceDefinition implements ServiceDefinitionInterface
{
    /**
     * The service's unique identifier
     *
     * @var string
     */
    protected string $id;

    /**
     * Defines whether the service should be a singleton
     *
     * @var bool
     */
    protected bool $shared;

    /**
     * The class' interface name
     *
     * @var array
     */
    protected array $aliases = [];

    /**
     * The definitions this depends on
     *
     * @var array<ServiceDefinitionInterface>
     */
    protected array $dependencies;

    /**
     * ServiceDefinition constructor.
     * @param string $id
     * @param bool $shared
     * @param array $aliases
     * @param ServiceDefinitionInterface[] $dependencies
     */
    public function __construct(string $id, bool $shared = true, array $aliases = [], array $dependencies = [])
    {
        $this->id = $id;
        $this->shared = $shared;
        $this->aliases = $aliases;
        $this->dependencies = $dependencies;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function aliases(): array
    {
        return $this->aliases;
    }

    public function dependencies(): array
    {
        return $this->dependencies;
    }

    public function makeNotShared(): void
    {
        $this->shared = false;
    }

    public function makeShared(): void
    {
        $this->shared = true;
    }
}
