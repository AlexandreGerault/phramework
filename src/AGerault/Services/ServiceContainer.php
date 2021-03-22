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
 * This class is a container for services. Its services array is
 * filled at the construction and then never modified: it is
 * immutable. Thus it avoid side effects.
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
        if (! class_exists($id)) {
            throw new ContainerException();
        }

        // If we have no instances of this id, let's build one
        if (! $this->has($id)) {
            $reflectionClass = new ReflectionClass($id);

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
                                throw new ContainerException();
                            }
                        },
                        $parameters
                    )
                );
            }
        }

        return $this->instances[$id];
    }

    #[Pure]
    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }
}
