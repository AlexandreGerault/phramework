<?php

use AGerault\Framework\Services\ServiceDefinition;

it(
    'should return the definition aliases',
    function () {
        $definition = new ServiceDefinition('ServiceId', true, ['a', 'b'], []);

        expect($definition->aliases())->toBe(['a', 'b']);
    }
);

it(
    'should return the definition dependencies',
    function () {
        $definition = new ServiceDefinition('ServiceId', true, ['a', 'b'], []);

        expect($definition->dependencies())->toBe([]);
    }
);

it(
    'should return whether the service is shared',
    function () {
        $definition = new ServiceDefinition('ServiceId', true, ['a', 'b'], []);

        expect($definition->isShared())->toBeTrue();
    }
);

it(
    'should make the service shared',
    function () {
        $definition = new ServiceDefinition('ServiceId', false, ['a', 'b'], []);

        expect($definition->isShared())->toBeFalse();

        $definition->makeShared();

        expect($definition->isShared())->toBeTrue();

        $definition->makeNotShared();

        expect($definition->isShared())->toBeFalse();
    }
);
