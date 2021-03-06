<?php

namespace AGerault\Framework\Services;

use AGerault\Framework\Contracts\Services\ServiceContainerInterface;
use AGerault\Framework\Contracts\Services\ServiceDefinitionInterface;
use AGerault\Framework\Services\Exceptions\ContainerException;
use AGerault\Framework\Services\Exceptions\ServiceNotFoundException;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
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
     * @var array<ServiceDefinitionInterface>
     */
    protected array $definitions = [];

    /**
     * @var array<string, mixed>
     */
    protected array $parameters = [];

    /**
     * @var array<string, callable>
     */
    protected array $factories = [];

    /**
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     * @throws ContainerException
     */
    public function get(string $id): mixed
    {
        if (!class_exists($id) && !interface_exists($id)) {
            throw new ServiceNotFoundException($id);
        }

        if (!$this->has($id)) {
            $instance = $this->getDefinition($id)->newInstance($this);

            if (!$this->getDefinition($id)->isShared()) {
                return $instance;
            }

            $this->instances[$id] = $instance;
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

    /**
     * Build each dependency
     *
     * @param ReflectionParameter[] $parameters
     * @return array<mixed>
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function buildDependencies(array $parameters): array
    {
        return array_map(
            function (ReflectionParameter $param) {
                $paramType = $param->getType();

                if ($paramType instanceof ReflectionNamedType) {
                    return $this->getDefinition($paramType->getName());
                }
            },
            array_filter(
                $parameters,
                function (ReflectionParameter $param) {
                    $type = $param->getType();
                    if ($type instanceof ReflectionNamedType) {
                        return !$type->isBuiltin();
                    } elseif ($type instanceof ReflectionUnionType) {
                        foreach ($type->getTypes() as $t) {
                            if (!$t->isBuiltin()) {
                                return false;
                            }
                        }
                    }
                }
            )
        );
    }

    /**
     * @param array<mixed> $parameters
     * @return array<mixed>
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function buildParameters(array $parameters): array
    {
        return array_map(
            function (ReflectionParameter $param) {
                $paramType = $param->getType();

                if ($paramType instanceof ReflectionNamedType) {
                    if ($paramType->isBuiltin()) {
                        return $this->getParameter($param->getName());
                    }
                    return $this->get($paramType->getName());
                }

                throw new ContainerException("Cannot use UnionTypeParameter in constructor");
            },
            $parameters
        );
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    public function register(string $id): void
    {
        if (!class_exists($id) && !interface_exists($id)) {
            throw new ServiceNotFoundException(sprintf("[%s] does not exist as a class or interface", $id));
        }

        $reflectionClass = new ReflectionClass($id);

        if ($reflectionClass->isInterface()) {
            $this->register($this->aliases[$id]);
            $this->definitions[$id] = &$this->definitions[$this->aliases[$id]];
            return;
        }

        $constructor = $reflectionClass->getConstructor();
        $dependencies = [];

        if ($constructor !== null) {
            $dependencies = $this->buildDependencies($constructor->getParameters());
        }

        $aliases = array_filter($this->aliases, fn(string $alias) => $alias === $id);

        $factory = null;
        if (isset($this->factories[$id])) {
            $factory = [
                "factory" => new ReflectionMethod(
                    $this->factories[$id]["class"],
                    $this->factories[$id]["method"]
                )
            ];
            if (isset($this->factories[$id]["args"])) {
                $factory["args"] = $this->factories[$id]["args"];
            }
        }

        $definition = new ServiceDefinition($id, true, $aliases, $dependencies, $factory["factory"] ?? null, $factory["args"] ?? []);


        $this->definitions[$id] = $definition;
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    public function getDefinition(string $id): ServiceDefinitionInterface
    {
        if (!isset($this->definitions[$id])) {
            $this->register($id);
        }

        return $this->definitions[$id];
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function resolve(string $id): object
    {
        if (!class_exists($id) && !interface_exists($id)) {
            throw new ServiceNotFoundException($id);
        }

        $reflectionClass = new ReflectionClass($id);

        // If we are handling an interface, we have to resolve to its class
        if ($reflectionClass->isInterface()) {
            return $this->resolve($this->aliases[$id]);
        }

        $this->getDefinition($id);

        if (array_key_exists($id, $this->factories)) {
            return call_user_func($this->factories[$id], $this);
        }

        // If the constructor of the class is null, no dependencies are required
        if ($reflectionClass->getConstructor() === null) {
            return $reflectionClass->newInstance();
        }

        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();

        return $reflectionClass->newInstanceArgs($this->buildParameters($parameters));
    }

    public function addParameter(string $id, mixed $value): void
    {
        $this->parameters[$id] = $value;
    }

    public function getParameter(string $id): mixed
    {
        return $this->parameters[$id];
    }

    public function addFactory(string $id, string $class, string $method, mixed ...$args): self
    {
        $factory = ["class" => $class, "method" => $method];

        if (count($args)) {
            $factory["args"] = $args;
        }

        $this->factories[$id] = $factory;

        return $this;
    }
}
