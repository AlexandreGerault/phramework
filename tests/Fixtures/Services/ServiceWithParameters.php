<?php

namespace Test\Fixtures\Services;

class ServiceWithParameters
{
    protected string $parameter;

    /**
     * ServiceWithParameters constructor.
     * @param string $parameter
     */
    public function __construct(string $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }
}
