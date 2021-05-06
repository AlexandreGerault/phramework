<?php

namespace AGerault\Framework\Routing;

use AGerault\Framework\Contracts\Routing\RouteCollectionInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array<RouteInterface>
     */
    protected array $routes = [];

    public function registerRoute(RouteInterface $route): void
    {
        $this->routes[] = $route;
    }

    public function routes(): array
    {
        return $this->routes;
    }
}
