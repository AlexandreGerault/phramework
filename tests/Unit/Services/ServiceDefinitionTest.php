<?php

use AGerault\Framework\Services\ServiceContainer;
use AGerault\Framework\Services\ServiceDefinition;
use Test\Fixtures\Services\BarService;
use Test\Fixtures\Services\DependsOnFactoryService;
use Test\Fixtures\Services\FactoryService;
use Test\Fixtures\Services\FooService;
use Test\Fixtures\Services\NotSharedService;

it(
    'should return the definition aliases',
    function () {
        $definition = new ServiceDefinition(DependsOnFactoryService::class, true, [FactoryService::class], []);

        expect($definition->aliases())->toBe([FactoryService::class]);
    }
);

it(
    'should return the definition dependencies',
    function () {
        $definition = new ServiceDefinition(FooService::class, true, [], []);

        expect($definition->dependencies())->toBe([]);
    }
);

it(
    'should return whether the service is shared',
    function () {
        $definition = new ServiceDefinition(NotSharedService::class, false, [], []);

        expect($definition->isShared())->toBeFalse();
    }
);

it(
    'should make the service shared',
    function () {
        $definition = new ServiceDefinition(FooService::class, false, [], []);

        expect($definition->isShared())->toBeFalse();

        $definition->makeShared();

        expect($definition->isShared())->toBeTrue();

        $definition->makeNotShared();

        expect($definition->isShared())->toBeFalse();
    }
);

it("should be able to build new instance using the container instance", function () {
    $container = new ServiceContainer();
    $definition = new ServiceDefinition(BarService::class);

    $instance = $definition->newInstance($container);

    expect($instance)->toBeInstanceOf(BarService::class);
});
