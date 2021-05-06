<?php

use AGerault\Framework\Contracts\Routing\RouteCollectionInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;
use AGerault\Framework\Routing\Exceptions\RouteNotFoundException;
use AGerault\Framework\Routing\UrlMatcher;

it(
    'should return a matching route without parameters',
    function () {
        $routeCollection = new class implements RouteCollectionInterface {
            public function registerRoute(RouteInterface $route): void
            {
            }

            public function routes(): array
            {
                return [
                    new class implements RouteInterface {
                        public function name(): string
                        {
                            return "dummy.route";
                        }

                        public function callback(): callable
                        {
                            return function () {
                                echo "Dummy route";
                            };
                        }

                        public function method(): string
                        {
                            return "POST";
                        }

                        public function parameter(string $id): mixed
                        {
                            return null;
                        }

                        public function parameterNames(): array
                        {
                            return [];
                        }

                        public function url(): string
                        {
                            return "dummy";
                        }

                        public function parameters(): array
                        {
                            return [];
                        }

                        public function setParameter(string $parameterName, mixed $value): void
                        {
                        }
                    },
                    new class implements RouteInterface {
                        public function name(): string
                        {
                            return "dummy.route";
                        }

                        public function callback(): callable
                        {
                            return function () {
                                echo "Dummy route";
                            };
                        }

                        public function method(): string
                        {
                            return "GET";
                        }

                        public function parameter(string $id): mixed
                        {
                            return null;
                        }

                        public function parameterNames(): array
                        {
                            return [];
                        }

                        public function url(): string
                        {
                            return "dummy";
                        }

                        public function parameters(): array
                        {
                            return [];
                        }

                        public function setParameter(string $parameterName, mixed $value): void
                        {
                        }
                    },
                    new class implements RouteInterface {
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
                            return null;
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
                    }
                ];
            }
        };
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

        $collection = new class ($route) implements RouteCollectionInterface {
            private array $routes = [];

            /**
             *  constructor.
             */
            public function __construct(RouteInterface $route)
            {
                $this->routes[] = $route;
            }

            public function registerRoute(RouteInterface $route): void
            {
            }

            public function routes(): array
            {
                return $this->routes;
            }
        };

        $matcher = new UrlMatcher($collection);

        $route = $matcher->match("article/mon-titre-d-article", "GET");
        expect($route)->toBeInstanceOf(RouteInterface::class);
    }
);

it('should throw an exception if no route matches', function () {
    $matcher = new UrlMatcher(new class implements RouteCollectionInterface {
        public function registerRoute(RouteInterface $route): void
        {
        }

        public function routes(): array
        {
            return [];
        }
    });
    $matcher->match("/some-random-route", "GET");
})->throws(RouteNotFoundException::class);
