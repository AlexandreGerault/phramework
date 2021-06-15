<?php

namespace AGerault\Framework\Contracts\Validation;

interface RuleInterface
{
    public function validate(): bool;

    public function onFailMessage(): string;
}
