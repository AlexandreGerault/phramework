<?php

namespace AGerault\Framework\Routing;

use AGerault\Framework\Contracts\HTTP\MiddlewareInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;
use AGerault\Framework\Routing\Exceptions\HttpVerbNotAllowedException;
use AGerault\Framework\Routing\Exceptions\ParameterNotFoundException;
use Exception;

class Route implements RouteInterface
{
    private const ALLOWED_VERBS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    protected string $path;

    protected string $name;

    protected string $method;

    /** @var MiddlewareInterface[]  */
    protected array $middlewares = [];

    /** @var array<string> */
    protected array $parameterNames = [];

    /** @var array<string, mixed> */
    protected array $parameters = [];

    /** @var callable */
    protected $action;

    /**
     * Route constructor.
     * @param string $path
     * @param string $name
     * @param string $method
     * @param callable $action
     * @param array<string> $parameterNames
     * @param array<MiddlewareInterface> $middlewares
     * @throws Exception
     */
    public function __construct(
        string $path,
        string $name,
        string $method,
        callable $action,
        array $parameterNames = [],
        array $middlewares = []
    ) {
        $this->name = $name;
        $this->parameterNames = $parameterNames;
        $this->action = $action;
        $this->setPath($path);
        $this->setMethod($method);
        $this->middlewares = $middlewares ?? [];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function callback(): callable
    {
        return $this->action;
    }

    public function method(): string
    {
        return $this->method;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws Exception
     */
    public function parameter(string $id): mixed
    {
        if (!array_key_exists($id, $this->parameters)) {
            throw new ParameterNotFoundException("Route parameter cannot be found");
        }

        return $this->parameters[$id];
    }

    public function parameterNames(): array
    {
        return $this->parameterNames;
    }

    public function url(): string
    {
        return $this->path;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function setParameter(string $parameterName, mixed $value): void
    {
        $this->parameters[$parameterName] = $value;
    }

    private function setPath(string $path): void
    {
        $this->path = trim($path, '/');
    }

    /**
     * @param string $method
     * @throws Exception
     */
    private function setMethod(string $method): void
    {
        $method = strtoupper($method);

        if (!in_array($method, self::ALLOWED_VERBS)) {
            throw new HttpVerbNotAllowedException("This HTTP verb isn't allowed");
        }

        $this->method = $method;
    }

    public function middlewares(): array
    {
        return $this->middlewares;
    }
}
