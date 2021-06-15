<?php

namespace AGerault\Framework\Contracts\Validation;

interface ValidatorInterface
{
    /**
     * @return bool Return whether all the rules passed
     */
    public function isValid(): bool;

    /**
     * @return array An associative array for errors
     */
    public function validate(): array;

    /**
     * @return array All fields passing their rules
     */
    public function validated(): array;

    /**
     * @return array An associative array to match fields with a set of rules
     */
    public function rules(): array;

    /**
     * @return array An associative array with inputs name and value
     */
    public function inputs(): array;

    /**
     * @param string $field
     * @return mixed The input value
     */
    public function input(string $field): mixed;

    /**
     * @return array
     */
    public function errors(): array;
}
