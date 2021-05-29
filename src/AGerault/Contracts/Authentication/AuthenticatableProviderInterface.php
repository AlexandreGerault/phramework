<?php

namespace AGerault\Framework\Contracts\Authentication;

interface AuthenticatableProviderInterface
{
    public function fetch(string|int $key): AuthenticatableInterface;
}
