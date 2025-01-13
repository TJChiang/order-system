<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:200',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'stock' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
