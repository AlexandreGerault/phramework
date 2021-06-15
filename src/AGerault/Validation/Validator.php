<?php

namespace AGerault\Framework\Validation;

use AGerault\Framework\Contracts\Validation\ValidatorInterface;
use AGerault\Framework\Validation\Rules\Rule;

abstract class Validator implements ValidatorInterface
{
    protected array $validated;
    protected array $errors;

    public function __construct(protected array $inputs) {}

    public function isValid(): bool
    {
        $this->errors = [];

        foreach ($this->rules() as $field => $ruleSet) {
            $value = $this->inputs[$field];

            /**
             * @var Rule $rule
             */
            foreach ($ruleSet as $rule) {
                $assertion = call_user_func($rule);
                if (! $assertion) {
                    $this->errors[$rule::class] = $rule->onFailMessage();
                }
            }
            $this->validated[$field] = $value;
        }

        return count($this->errors) === 0;
    }

    public function validated(): array
    {
        $this->isValid();
        return $this->validated;
    }

    public function validate(): array
    {
        return $this->isValid() ? [] : $this->errors;
    }

    public function inputs(): array
    {
        return $this->inputs;
    }

    public function input(string $field): mixed
    {
        return $this->inputs[$field];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
