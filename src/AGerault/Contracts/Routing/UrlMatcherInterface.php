<?php

namespace AGerault\Framework\Contracts\Routing;

use AGerault\Framework\Routing\Exceptions\RouteNotFoundException;

interface UrlMatcherInterface
{
    /**
     * @param string $uri
     * @param string $method
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function match(string $uri, string $method): RouteInterface;
}
