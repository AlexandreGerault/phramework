<?php

use AGerault\Framework\HTTP\HttpRequestHandler;
use AGerault\Framework\HTTP\MiddlewarePipe;
use AGerault\Framework\HTTP\NoResponseCreatedException;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Test\Fixtures\HTTP\AddDummyHeaderToResponseMiddleware;
use Test\Fixtures\HTTP\ReturnDummyResponseMiddleware;

it(
    'should process one middleware',
    function () {
        $pipe = new MiddlewarePipe([new ReturnDummyResponseMiddleware()]);
        $handler = new HttpRequestHandler($pipe);

        $request = new ServerRequest("GET", "/");

        $response = $handler->handle($request);
        expect($response)->toBeInstanceOf(ResponseInterface::class);
    }
);

it(
    'should throw a no response created exception if no middleware is provided',
    function () {
        $pipe = new MiddlewarePipe([]);
        $handler = new HttpRequestHandler($pipe);

        $request = new ServerRequest("GET", "/");

        $handler->handle($request);
    }
)->expectException(NoResponseCreatedException::class);

it(
    'should throw an exception if no middleware create a response',
    function () {
        $pipe = new MiddlewarePipe();
        $pipe->push(new AddDummyHeaderToResponseMiddleware());
        $handler = new HttpRequestHandler($pipe);

        $request = new ServerRequest("GET", "/");

        $handler->handle($request);
    }
)->expectException(NoResponseCreatedException::class);;
