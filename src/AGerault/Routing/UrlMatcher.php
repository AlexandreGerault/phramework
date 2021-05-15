<?php

namespace AGerault\Framework\Routing;

use AGerault\Framework\Contracts\Routing\RouteCollectionInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;
use AGerault\Framework\Contracts\Routing\UrlMatcherInterface;
use AGerault\Framework\Routing\Exceptions\RouteNotFoundException;
use Exception;

class UrlMatcher implements UrlMatcherInterface
{
    protected RouteCollectionInterface $routeCollection;

    /**
     * UrlMatcher constructor.
     * @param RouteCollectionInterface $routeCollection
     */
    public function __construct(RouteCollectionInterface $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    /**
     * @param string $uri
     * @param string $method
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function match(string $uri, string $method): RouteInterface
    {
        foreach ($this->routeCollection->routes() as $route) {
            if ($route->method() !== $method) {
                continue;
            }

            $uri = trim($uri, '/');
            $matches = [];

            if (!preg_match("`^{$route->url()}$`", $uri, $matches)) {
                continue;
            }
            array_shift($matches);

            foreach ($route->parameterNames() as $parameter) {
                $route->setParameter($parameter, array_shift($matches));
            }

            return $route;
        }

        throw new RouteNotFoundException("Route cannot be found");
    }
}
