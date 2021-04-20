<?php

use AGerault\Framework\Contracts\Routing\RouteInterface;
use AGerault\Framework\Routing\RouteCollection;
use AGerault\Framework\Routing\UrlMatcher;

it(
    'should return a matching route without parameters',
    function () {
        $route = new class implements RouteInterface {
            public function name(): string
            {
                return "article.show";
            }

            public function callback(): callable
            {
                return function () {
                    echo "Show article";
                };
            }

            public function method(): string
            {
                return "GET";
            }

            public function parameter(string $id): mixed
            {
            }

            public function parameterNames(): array
            {
                return [];
            }

            public function url(): string
            {
                return "article";
            }

            public function parameters(): array
            {
                return [];
            }

            public function setParameter(string $parameterName, mixed $value): void
            {
            }
        };

        $routeCollection = new RouteCollection();
        $routeCollection->registerRoute($route);
        $matcher = new UrlMatcher($routeCollection);

        $route = $matcher->match("article", "GET");
        expect($route)->toBeInstanceOf(RouteInterface::class);
    }
);

it(
    'should return a matching route with parameters',
    function () {
        $route = new class implements RouteInterface {
            public function name(): string
            {
                return "article.show";
            }

            public function callback(): callable
            {
                return function () {
                    echo "Show article";
                };
            }

            public function method(): string
            {
                return "GET";
            }

            public function parameter(string $id): mixed
            {
            }

            public function parameterNames(): array
            {
                return ["slug"];
            }

            public function url(): string
            {
                return "article/(.+)";
            }

            public function parameters(): array
            {
                return ["slug" => "mon slug"];
            }

            public function setParameter(string $parameterName, mixed $value): void
            {
            }
        };

        $routeCollection = new RouteCollection();
        $routeCollection->registerRoute($route);
        $matcher = new UrlMatcher($routeCollection);

        $route = $matcher->match("article/mon-titre-d-article", "GET");
        expect($route)->toBeInstanceOf(RouteInterface::class);
    }
);

it('should throw an exception if no route matches', function() {
    $matcher = new UrlMatcher(new RouteCollection());
    $matcher->match("/some-random-route", "GET");
})->throws(Exception::class);
