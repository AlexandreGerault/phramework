<?php

namespace AGerault\Framework\Contracts\Routing;

interface RouteInterface
{
    public function name(): string;

    public function callback(): callable;

    public function method(): string;

    public function parameter(string $id): mixed;


    /**
     * @return array<string>
     */
    public function parameterNames(): array;

    public function url(): string;


    /**
     * @return array<string, mixed>
     */
    public function parameters(): array;

    public function setParameter(string $parameterName, mixed $value): void;
}
