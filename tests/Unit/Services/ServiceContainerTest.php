<?php

use AGerault\Framework\Services\Exceptions\ContainerException;
use AGerault\Framework\Services\Exceptions\ServiceNotFoundException;
use AGerault\Framework\Services\ServiceContainer;
use Test\Fixtures\Services\BarService;
use Test\Fixtures\Services\DependsOnFactoryService;
use Test\Fixtures\Services\Factory;
use Test\Fixtures\Services\FactoryService;
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
    'should throw a not found exception if the class or interface does not exist',
    function () {
        $container = new ServiceContainer();

        $container->get('Foo');
    }
)->throws(ServiceNotFoundException::class);

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

        $container->get(UnionTypeServiceInjection::class);
    }
)->throws(ContainerException::class);

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

it(
    'throws an exception when trying to register a non existing class',
    function () {
        $container = new ServiceContainer();

        $container->register('a');
    }
)->throws(ServiceNotFoundException::class);

it(
    'should be able to register a factory and call it to instantiate a service',
    function () {
        $container = new ServiceContainer();

        $container->addFactory(FactoryService::class, Factory::class, 'makeFactoryService', "Name");

        $service = $container->get(FactoryService::class);

        expect($service)->toBeInstanceOf(FactoryService::class);
        expect($service->name())->toBe("Name");
    }
);
