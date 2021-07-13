<?php

namespace AGerault\Framework\HTTP;

use AGerault\Framework\Contracts\HTTP\MiddlewareInterface;
use AGerault\Framework\Contracts\HTTP\MiddlewarePipeInterface;
use Generator;

class MiddlewarePipe implements MiddlewarePipeInterface
{
    public function __construct(protected array $middlewares = []) {}

    public function push(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function generator(): Generator
    {
        foreach ($this->middlewares as $middleware) {
            yield $middleware;
        }
    }
}
