<?php

namespace AGerault\Framework\Validation\Rules;

use AGerault\Framework\Contracts\Validation\RuleInterface;

abstract class Rule implements RuleInterface
{
    protected string $message = "";
    protected mixed $value;

    /**
     * Rule constructor.
     * @param mixed $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function __invoke(): bool
    {
        return $this->validate();
    }
}
