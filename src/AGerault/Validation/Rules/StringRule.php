<?php

namespace AGerault\Framework\Validation\Rules;

use Assert\Assert;
use Assert\AssertionFailedException;

class StringRule extends Rule
{
    public function validate(): bool
    {
        return is_string($this->value);
    }

    public function onFailMessage(): string
    {
        return "{$this->value} is not a valid string";
    }
}
