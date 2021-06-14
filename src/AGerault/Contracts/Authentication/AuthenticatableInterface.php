<?php

namespace AGerault\Framework\Contracts\Authentication;

interface AuthenticatableInterface
{
    /**
     * A unique identifier to fetch a user
     */
    public function key(): int|string;
    
    public function login(): string;
    
    public function password(): string;
}
