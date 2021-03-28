<?php

use AGerault\Framework\Services\ServiceContainer;
use Test\Fixtures\Services\BarService;
use Test\Fixtures\Services\FooService;
use Test\Fixtures\Services\NotSharedService;
use Test\Fixtures\Services\RandomService;
use Test\Fixtures\Services\RandomServiceInterface;
use Test\Fixtures\Services\ServiceWithParameters;
use Test\Fixtures\Services\UnionTypeServiceInjection;

it(
    'checks the type of returned class by the container',
    function () {
        $container = new ServiceContainer();

        expect($container->get(FooService::class))->toBeInstanceOf(FooService::class);
    }
);

it(
    'should return the same service instance',
    function () {
        $container = new ServiceContainer();

        expect($container->get(FooService::class))->toBe($container->get(FooService::class));
    }
);

it(
    'should build classes and its dependencies',
    function () {
        $container = new ServiceContainer();

        expect($container->get(BarService::class))->toBeInstanceOf(BarService::class);
        expect($container->has(FooService::class))->toBeTrue();
    }
);

it(
    'should throws an exception when trying to use an union type in service constructor',
    function () {
        $container = new ServiceContainer();

        expect($container->get(UnionTypeServiceInjection::class))->toBeInstanceOf(UnionTypeServiceInjection::class);
    }
)->throws(Exception::class);

it(
    'should be able to inject interfaces implementation',
    function () {
        $container = new ServiceContainer();

        $container->addAlias(RandomServiceInterface::class, RandomService::class);

        $container->get(RandomServiceInterface::class);

        expect($container->get(RandomServiceInterface::class))->toBeInstanceOf(RandomServiceInterface::class);
    }
);

it(
    'should return a different service instance if it is not shared',
    function () {
        $container = new ServiceContainer();

        $container->getDefinition(NotSharedService::class)->makeNotShared();
        $instanceA = $container->get(NotSharedService::class);
        $instanceB = $container->get(NotSharedService::class);

        expect(spl_object_id($instanceA))->not()->toBe(spl_object_id($instanceB));
    }
);

it(
    'should be able to build a service depending on parameters',
    function () {
        $container = new ServiceContainer();

        $container->addParameter('parameter', 'Parameter');
        /**
         * @var ServiceWithParameters $service
         */
        $service = $container->get(ServiceWithParameters::class);

        expect($service->getParameter())->toBe('Parameter');
    }
);
