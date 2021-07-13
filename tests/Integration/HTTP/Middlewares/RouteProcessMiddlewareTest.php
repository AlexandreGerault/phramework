<?php

use AGerault\Framework\HTTP\Middlewares\RouteProcessMiddleware;
use AGerault\Framework\Routing\Route;
use AGerault\Framework\Routing\RouteCollection;
use AGerault\Framework\Routing\UrlMatcher;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Test\Fixtures\HTTP\DummyHandler;

it(
    'should return a response',
    function () {
        $route = new Route(
            '/', 'home', 'GET', function () {
            return new Response(200, [], "Homepage");
        }
        );
        $collection = new RouteCollection();
        $collection->registerRoute($route);
        $matcher = new UrlMatcher($collection);
        $request = new ServerRequest("GET", "/");

        $middleware = new RouteProcessMiddleware($matcher);

        $response = $middleware->process($request, new DummyHandler());

        expect($response->getStatusCode())->toBe(200);
        expect($response->getBody()->getContents())->toBe("Homepage");
    }
);

it(
    'should return a 404 response',
    function () {
        $collection = new RouteCollection();
        $matcher = new UrlMatcher($collection);
        $request = new ServerRequest("GET", "/");

        $middleware = new RouteProcessMiddleware($matcher);

        $response = $middleware->process($request, new DummyHandler());

        expect($response->getStatusCode())->toBe(404);
    }
);
