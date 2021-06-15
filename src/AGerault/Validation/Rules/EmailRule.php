<?php

namespace AGerault\Framework\Validation\Rules;

use Assert\Assertion;
use Assert\AssertionFailedException;

class EmailRule extends Rule
{
    public function validate(): bool
    {
        try {
            Assertion::email($this->value);
            return true;
        } catch (AssertionFailedException) {
            return false;
        }
    }

    public function onFailMessage(): string
    {
        return "{$this->value} is not a valid email address";
    }
}
