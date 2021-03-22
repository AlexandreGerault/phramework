<?php

namespace AGerault\Framework\Services\Exceptions;

use AGerault\Framework\Contracts\Services\ServiceNotFoundExceptionInterface;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ServiceNotFoundException extends \Exception implements ServiceNotFoundExceptionInterface
{
    #[Pure]
    public function __construct(string $serviceId = "")
    {
        parent::__construct("Cannot find the service identified by " . $serviceId);
    }
}
