<?php

namespace AGerault\Framework\Authentication;

use AGerault\Framework\Contracts\Authentication\AuthenticatableInterface;
use AGerault\Framework\Contracts\Authentication\AuthenticatorInterface;

class Authenticator implements AuthenticatorInterface
{

    public function check(AuthenticatableInterface $authenticatable, array $credentials): bool
    {
        return true;
    }

    public function attempt(AuthenticatableInterface $authenticatable, array $credentials): ?AuthenticatableInterface
    {
        return $credentials['login'] === $authenticatable->login()
               && password_verify($credentials['password'], $authenticatable->password()) ? $authenticatable : null;
    }

}
