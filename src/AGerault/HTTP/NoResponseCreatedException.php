<?php

namespace AGerault\Framework\HTTP;

use Exception;

class NoResponseCreatedException extends Exception
{
    protected $message = "No response could be created";
}
