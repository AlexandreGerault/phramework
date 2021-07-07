<?php

namespace AGerault\Framework\Contracts\HTTP;

use Generator;

interface MiddlewarePipeInterface
{
    public function push(MiddlewareInterface $middleware): void;

    public function generator(): Generator;
}
