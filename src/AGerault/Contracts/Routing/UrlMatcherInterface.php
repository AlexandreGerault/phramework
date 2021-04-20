<?php

namespace AGerault\Framework\Contracts\Routing;

interface UrlMatcherInterface
{
    public function match(string $uri, string $method): RouteInterface;
}
