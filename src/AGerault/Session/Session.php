<?php

namespace AGerault\Framework\Session;

use AGerault\Framework\Contracts\Session\SessionInterface;

class Session implements SessionInterface
{
    public function __construct()
    {
        session_start();
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function put(string $key, mixed $payload): void
    {
        $_SESSION[$key] = $payload;
    }

    public function clear(): void
    {
        session_destroy();
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
