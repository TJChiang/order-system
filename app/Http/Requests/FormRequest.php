<?php

namespace App\Http\Requests;

use App\Exceptions\General\InvalidArgumentException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        foreach ($validator->errors()->messages() as $field => $msgBag) {
            foreach ($msgBag as $msg) {
                throw new InvalidArgumentException("Field: {$field}. Error: {$msg}");
            }
        }
    }
}
