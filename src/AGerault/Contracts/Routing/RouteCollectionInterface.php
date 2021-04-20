<?php


namespace AGerault\Framework\Contracts\Routing;


interface RouteCollectionInterface
{
    public function registerRoute(RouteInterface $route): void;

    /**
     * @return array<RouteInterface>
     */
    public function routes(): array;
}
