<?php

namespace AGerault\Framework\Contracts\Session;

interface SessionInterface
{
    public function has(string $key): bool;

    public function get(string $key): mixed;

    public function put(string $key, mixed $payload): void;

    public function clear(): void;

    public function forget(string $key): void;
}
