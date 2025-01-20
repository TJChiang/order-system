<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;

class GetListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => [
                'filled',
                'integer',
                'min:1',
            ],
            'shipment_id' => [
                'filled',
                'integer',
                'min:1',
            ],
            'shipment_number' => [
                'filled',
                'string',
                'max:255',
            ],
            'courier' => [
                'filled',
                'string',
                'max:255',
            ],
            'tracking_number' => [
                'filled',
                'string',
                'max:255',
            ],
            'status' => [
                'filled',
                'integer',
                'min:0',
            ],
            'start_shipped_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'before_or_equal:end_shipped_time',
            ],
            'end_shipped_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:start_shipped_time',
            ],
            'start_delivered_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'before_or_equal:end_delivered_time',
            ],
            'end_delivered_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:start_delivered_time',
            ],
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
        ];
    }
}
