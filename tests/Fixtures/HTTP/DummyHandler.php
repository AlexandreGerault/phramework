<?php


namespace Test\Fixtures\HTTP;


use AGerault\Framework\Contracts\HTTP\HttpRequestHandlerInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DummyHandler implements HttpRequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], "Response");
    }
}
