<?php

namespace Test\Fixtures\Services;

class ServiceDependingOnNonExistingClass
{
    protected NonExistingClass $class;

    /**
     * ServiceDependingOnNonExistingClass constructor.
     * @param NonExistingClass $class
     */
    public function __construct(NonExistingClass $class)
    {
        $this->class = $class;
    }
}
