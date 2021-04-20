<?php

use AGerault\Framework\Routing\RouteCollection;
use Test\Fixtures\Routing\MockedRoute;

it('should increase the routes number after registering one',function() {
    $collection = new RouteCollection();
    $route = new MockedRoute();

    $collection->registerRoute($route);

    expect($collection->routes())
        ->toBeArray()
        ->toContain($route)
        ->toHaveCount(1);
});
