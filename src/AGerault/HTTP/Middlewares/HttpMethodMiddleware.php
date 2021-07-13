<?php

namespace AGerault\Framework\HTTP\Middlewares;

use AGerault\Framework\Contracts\HTTP\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpMethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getAttribute('_method') ?? $request->getMethod();

        if ($request->getAttribute('_method')) {
            $request = $request->withMethod($method);
        }

        return $handler->handle($request);
    }
}
