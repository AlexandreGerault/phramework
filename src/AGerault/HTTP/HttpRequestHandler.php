<?php

namespace AGerault\Framework\HTTP;

use AGerault\Framework\Contracts\HTTP\HttpRequestHandlerInterface;
use AGerault\Framework\Contracts\HTTP\MiddlewarePipeInterface;
use Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpRequestHandler implements HttpRequestHandlerInterface
{
    private Generator $generator;

    public function __construct(protected MiddlewarePipeInterface $pipe) {
        $this->generator = $this->pipe->generator();
    }

    /**
     * @throws NoResponseCreatedException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->generator->current();
        $this->generator->next();

        if ($middleware !== null) {
            return $middleware->process($request, $this);
        }

        throw new NoResponseCreatedException();
    }
}
