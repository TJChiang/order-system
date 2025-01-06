<?php

namespace App\Rules;

interface Validable
{
    public function isValid(mixed $value): bool;

    public function message(string $attribute, mixed $value): string;
}
