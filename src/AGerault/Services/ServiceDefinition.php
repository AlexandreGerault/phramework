<?php

namespace AGerault\Framework\Services;

use AGerault\Framework\Contracts\Services\ServiceDefinitionInterface;
use AGerault\Framework\Services\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

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
     * @var array<string>
     */
    protected array $aliases = [];

    /**
     * The definitions this depends on
     *
     * @var array<ServiceDefinitionInterface>
     */
    protected array $dependencies;

    /**
     * A reflection class to access information on the code
     *
     * @var ReflectionClass
     */
    private ReflectionClass $class;

    /**
     * A factory for some services that needs to be built
     * using environment variables for example
     *
     * @var ReflectionMethod|null
     */
    private ?ReflectionMethod $factory;

    private array $factoryArgs;

    /**
     * ServiceDefinition constructor.
     * @param string $id
     * @param bool $shared
     * @param array<string> $aliases
     * @param ServiceDefinitionInterface[] $dependencies
     * @param null $factory
     * @throws ReflectionException
     */
    public function __construct(
        string $id,
        bool $shared = true,
        array $aliases = [],
        array $dependencies = [],
        $factory = null,
        $factoryArgs = []
    ) {
        $this->id = $id;
        $this->shared = $shared;
        $this->aliases = $aliases;
        $this->dependencies = $dependencies;
        $this->class = new ReflectionClass($id);
        $this->factory = $factory;
        $this->factoryArgs = $factoryArgs;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @return array<string>
     */
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

    /**
     * @throws ReflectionException
     */
    public function newInstance(ServiceContainer $container): object
    {
        if ($this->factory !== null) {
            return $this->factory->invokeArgs(null, $this->factoryArgs);
        }

        $constructor = $this->class->getConstructor();

        // If the constructor of the class is null, no dependencies are required
        if ($constructor === null) {
            return $this->class->newInstance();
        }

        return $this->class->newInstanceArgs($this->resolve($container, $constructor));
    }

    /**
     * @param ContainerInterface $container
     * @param ReflectionMethod $method
     * @return array
     * @throws ContainerException
     */
    private function resolve(ContainerInterface $container, ReflectionMethod $method): array
    {
        return array_map(
            function (ReflectionParameter $param) use ($container) {
                $paramType = $param->getType();

                if ($paramType instanceof ReflectionNamedType) {
                    if ($paramType->isBuiltin()) {
                        return $container->getParameter($param->getName());
                    }
                    return $container->get($paramType->getName());
                }

                throw new ContainerException("Cannot use UnionTypeParameter in constructor");
            },
            $method->getParameters()
        );
    }
}
