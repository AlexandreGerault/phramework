<?php

use AGerault\Framework\Routing\Exceptions\HttpVerbNotAllowedException;
use AGerault\Framework\Routing\Exceptions\ParameterNotFoundException;
use AGerault\Framework\Routing\Route;
use GuzzleHttp\Psr7\Response;

it(
    'should throw an exception if it use a wrong HTTP verb',
    function () {
        new Route(
            '/',
            'name',
            'BLABLA',
            function () {
            }
        );
    }
)->throws(HttpVerbNotAllowedException::class);

it(
    'should throw an exception if the action is not callable',
    function () {
        new Route('/', 'name', 'GET', ["not a callable"]);
    }
)->throws(TypeError::class);

it(
    'should be able to execute the callback if it is a closure',
    function () {
        $route = new Route(
            '/',
            'name',
            'GET',
            function () {
                return "Working closure";
            }
        );

        $action = $route->callback();

        expect($action())->toBeString()->toEqual("Working closure");
    }
);

it(
    'should have working getters',
    function () {
        $route = new Route(
            '/route/(.+)/(\d+)',
            'name',
            'GET',
            function () {
                return "Working closure";
            },
            ["slug", "id"]
        );

        expect($route->name())->toEqual('name');
        expect($route->method())->toEqual('GET');
        expect($route->parameters())->toBeArray()->toEqual([]);
        expect($route->url())->toBeString()->toEqual("route/(.+)/(\d+)");
        expect($route->parameterNames())->toBeArray()->toEqual(["slug", "id"]);

        $route->setParameter('slug', 'ma-route');
        $route->setParameter('id', 1);
        expect($route->parameter('slug'))->toBeString()->toEqual('ma-route');
        expect($route->parameter('id'))->toBeInt()->toEqual(1);
        expect($route->parameters())->toBeArray()->toEqual(["slug" => "ma-route", "id" => 1]);
    }
);

it(
    'should throw an exception if trying a non set parameter',
    function () {
        $route = new Route(
            '/route/(.+)/(\d+)',
            'name',
            'GET',
            function () {
                return "Working closure";
            },
            ["slug", "id"]
        );

        $route->parameter('slug');
    }
)->throws(ParameterNotFoundException::class);

it(
    "should be able to call a route's callback when it's a callable given as an array",
    function () {
        $callback = new class {
            public function index()
            {
                return "index page";
            }
        };

        $route = new Route("/articles", "articles.index", "GET", [$callback, 'index']);

        expect(call_user_func_array($route->callback(), []))->toBe('index page');
    }
);

it("should be able to get route's middlewares", function () {
    $callback = new class {
        public function index()
        {
            return new Response(200, [], "");
        }
    };

    $route = new Route("/", "home", "GET", [$callback, 'index']);

    expect($route->middlewares())->toBeArray();
});
