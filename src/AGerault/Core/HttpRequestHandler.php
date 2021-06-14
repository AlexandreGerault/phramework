<?php

namespace AGerault\Framework\Core;

use AGerault\Framework\Contracts\Core\HttpRequestHandlerInterface;
use AGerault\Framework\Contracts\Routing\UrlMatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpRequestHandler implements HttpRequestHandlerInterface
{
    protected UrlMatcherInterface $matcher;

    public function __construct(UrlMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
            $route = $this->matcher->match(
                $request->getUri()->getPath(),
                $request->getMethod()
            );

            $callback = $route->callback();

            return call_user_func_array($callback, [$request] + $route->parameters() ?? []);
    }
}
