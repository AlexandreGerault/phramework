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
        if (array_key_exists('_method', $request->getParsedBody())) {
            $request = $request->withMethod($request->getParsedBody()['_method']);
        }

        return $handler->handle($request);
    }
}
