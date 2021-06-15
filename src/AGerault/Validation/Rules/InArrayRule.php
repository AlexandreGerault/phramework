<?php

namespace AGerault\Framework\Validation\Rules;

class InArrayRule extends Rule
{
    public function __construct(protected mixed $value, protected array $haystack)
    {
        parent::__construct($this->value);
    }

    public function validate(): bool
    {
        return in_array($this->value, $this->haystack);
    }

    public function onFailMessage(): string
    {
        return "Provided value is not in your array";
    }
}
