<?php

namespace Test\Fixtures\Routing;

use AGerault\Framework\Contracts\HTTP\MiddlewareInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;

class MockedRoute implements RouteInterface
{

    public function name(): string
    {
    }

    public function callback(): callable
    {
        return function () {
        };
    }

    public function method(): string
    {
        return '';
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
        return "";
    }

    public function parameters(): array
    {
        return [];
    }

    public function setParameter(string $parameterName, mixed $value): void
    {
    }

    public function middlewares(): array
    {
        return [];
    }
}
