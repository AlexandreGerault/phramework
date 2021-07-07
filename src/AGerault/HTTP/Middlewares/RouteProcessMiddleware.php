<?php

namespace AGerault\Framework\HTTP\Middlewares;

use AGerault\Framework\Contracts\HTTP\MiddlewareInterface;
use AGerault\Framework\Contracts\Routing\UrlMatcherInterface;
use AGerault\Framework\Routing\Exceptions\RouteNotFoundException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteProcessMiddleware implements MiddlewareInterface
{
    public function __construct(protected UrlMatcherInterface $matcher) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $route = $this->matcher->match(
                $request->getUri()->getPath(),
                $request->getMethod()
            );

            $callback = $route->callback();

            return call_user_func_array($callback, [$request] + $route->parameters() ?? []);
        } catch (RouteNotFoundException $exception) {
            return new Response(404, [], $exception->getMessage());
        }
    }
}
