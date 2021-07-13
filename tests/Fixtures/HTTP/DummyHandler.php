<?php


namespace Test\Fixtures\HTTP;


use AGerault\Framework\Contracts\HTTP\HttpRequestHandlerInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DummyHandler implements HttpRequestHandlerInterface
{
    protected ServerRequestInterface $request;

    public function __construct() {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return new Response(200, [], "Response");
    }

    public function request(): ServerRequestInterface
    {
        return $this->request;
    }
}
