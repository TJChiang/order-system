<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class GetListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => [
                'filled',
                'integer',
                'min:1',
            ],
            'limit' => [
                'filled',
                'integer',
                'min:1',
            ],
            'id' => [
                'filled',
                'integer',
                'min:1',
            ],
            'sku' => [
                'filled',
                'string',
                'max:100',
            ],
            'name' => [
                'filled',
                'string',
                'max:200',
            ],
            'status' => [
                'filled',
                'string',
                'min:0',
                'max:10',
            ],
            'version' => [
                'filled',
                'integer',
            ],
        ];
    }
}
