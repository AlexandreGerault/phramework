<?php

namespace AGerault\Framework\Services;

use AGerault\Framework\Contracts\Services\ServiceContainerInterface;
use AGerault\Framework\Services\Exceptions\ContainerException;
use AGerault\Framework\Services\Exceptions\ServiceNotFoundException;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Implements PSR-11 ContainerInterface for a service container
 * See: https://www.php-fig.org/psr/psr-11/
 *
 * This class is a container for services. Its responsibility
 * is to instantiate a service and its dependencies using
 * reflection and recursiveness.
 *
 * @package AGerault\Framework\Services
 * @author Alexandre Gérault
 */
class ServiceContainer implements ServiceContainerInterface
{
    /**
     * @var  array<string, string>
     */
    protected array $aliases = [];

    /**
     * @var array<string, mixed>
     */
    protected array $instances = [];

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ServiceNotFoundException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function get(string $id): mixed
    {
        if (! class_exists($id) && ! interface_exists($id)) {
            throw new ContainerException("This class does not exist");
        }

        // If we have no instances of this id, let's build one
        if (! $this->has($id)) {
            $reflectionClass = new ReflectionClass($id);

            // If we are handling an interface, we have to resolve to its class
            // Else we are dealing with a class and we build it
            if ($reflectionClass->isInterface()) {
                return $this->get($this->aliases[$id]);
            } else {
                // If the constructor of the class is null, no dependencies are required
                // Else we need to build each dependency
                if ($reflectionClass->getConstructor() === null) {
                    $this->instances[$id] = $reflectionClass->newInstance();
                } else {
                    $constructor = $reflectionClass->getConstructor();
                    $parameters  = $constructor->getParameters();

                    $this->instances[$id] = $reflectionClass->newInstanceArgs(
                        array_map(
                            function ($param) {
                                $paramType = $param->getType();

                                if ($paramType instanceof ReflectionNamedType) {
                                    return $this->get($paramType->getName());
                                } else {
                                    throw new ContainerException("Cannot use UnionTypeParameter in constructor");
                                }
                            },
                            $parameters
                        )
                    );
                }
            }
        }

        return $this->instances[$id];
    }

    #[Pure]
    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    public function addAlias(string $alias, string $target): void
    {
        $this->aliases[$alias] = $target;
    }
}
