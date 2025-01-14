<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class UpdateByIdRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
            ],
            'name' => [
                'filled',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'filled',
                'numeric',
            ],
            'stock' => [
                'filled',
                'integer',
            ],
            'status' => [
                'filled',
                'integer',
            ],
        ];
    }
}
