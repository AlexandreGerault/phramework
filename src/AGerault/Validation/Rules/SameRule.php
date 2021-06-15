<?php

namespace AGerault\Framework\Validation\Rules;

class SameRule extends Rule
{
    public function __construct(protected mixed $value, protected mixed $target)
    {
        parent::__construct($value);
    }

    public function validate(): bool
    {
        return $this->value === $this->target;
    }

    public function onFailMessage(): string
    {
        return "{$this->target} is not the same than {$this->value}";
    }
}
