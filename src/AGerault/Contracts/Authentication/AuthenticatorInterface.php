<?php

namespace AGerault\Framework\Contracts\Authentication;

interface AuthenticatorInterface
{
    /**
     * Check whether the credentials match for a given user
     */
    public function check(AuthenticatableInterface $authenticatable, array $credentials): bool;

    /**
     * Try to log in a given user based on credentials
     */
    public function attempt(AuthenticatableInterface $authenticatable, array $credentials): ?AuthenticatableInterface;
}
