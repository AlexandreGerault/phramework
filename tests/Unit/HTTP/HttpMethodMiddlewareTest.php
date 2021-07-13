<?php

use AGerault\Framework\HTTP\Middlewares\HttpMethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Test\Fixtures\HTTP\DummyHandler;

it('should change the request method', function () {
    $request = new ServerRequest("POST", "/");
    $request = $request->withAttribute("_method", "PUT");
    $middleware = new HttpMethodMiddleware();
    $handler = new DummyHandler();

    $middleware->process($request, $handler);

    expect($handler->request()->getMethod())->toBe("PUT");
});
