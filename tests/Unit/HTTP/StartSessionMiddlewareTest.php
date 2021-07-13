<?php

use AGerault\Framework\HTTP\Middlewares\StartSessionMiddleware;
use AGerault\Framework\Session\Session;
use GuzzleHttp\Psr7\ServerRequest;
use Test\Fixtures\HTTP\DummyHandler;

it(
    "should call the start method of the session interface",
    function () {
        $request = new ServerRequest("POST", "/");
        $request = $request->withAttribute("_method", "PUT");
        $session = Mockery::spy(Session::class);
        $middleware = new StartSessionMiddleware($session);
        $handler = new DummyHandler();

        $middleware->process($request, $handler);

        $session->shouldHaveReceived()->start();
    }
);
