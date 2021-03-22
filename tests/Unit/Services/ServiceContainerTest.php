<?php

use AGerault\Framework\Services\ServiceContainer;
use Test\Fixtures\Services\BarService;
use Test\Fixtures\Services\FooService;
use Test\Fixtures\Services\UnionTypeServiceInjection;

it(
    'checks the type of returned class by the container',
    function () {
        $container = new ServiceContainer();

        expect($container->get(FooService::class))->toBeInstanceOf(FooService::class);
    }
);

it(
    'checks that returned services have the same instance',
    function () {
        $container = new ServiceContainer();

        expect($container->get(FooService::class))->toBe($container->get(FooService::class));
    }
);

it(
    'checks that it can build classes depending on another service',
    function () {
        $container = new ServiceContainer();

        expect($container->get(BarService::class))->toBeInstanceOf(BarService::class);
        expect($container->has(FooService::class))->toBeTrue();
    }
);

it(
    'checks that it throws an exception when trying to use an union type in service constructor',
    function () {
        $container = new ServiceContainer();

        expect($container->get(UnionTypeServiceInjection::class))->toBeInstanceOf(UnionTypeServiceInjection::class);
    }
)->throws(Exception::class);
