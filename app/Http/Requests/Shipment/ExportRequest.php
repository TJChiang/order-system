<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;

class ExportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'shipment_number' => [
                'required',
                'string',
                'max:255',
                // 'uuid',
            ],
        ];
    }
}
