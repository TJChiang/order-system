<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Rules\PhoneRule;

class UpdateByIdRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 訂單
            'recipient_name' => [
                'filled',
                'string',
                'max:255',
            ],
            'recipient_email' => [
                'filled',
                'string',
                'email',
                'max:255',
            ],
            'recipient_phone' => [
                'filled',
                'string',
                'max:50',
                new PhoneRule(),
            ],
            'shipping_address' => [
                'filled',
                'string',
                'max:500',
            ],
            'scheduled_shipping_date' => [
                'filled',
                'date',
                'date_format:Y-m-d',
            ],
            'status' => [
                'filled',
                'integer',
                'min:0',
            ],
            'remark' => [
                'filled',
                'string',
                'max:400',
            ],
            // 出貨單
            'shipments' => [
                'filled',
                'array',
                'max:50',
            ],
            'shipments.*.id' => [
                'filled',
                'integer',
                'required_with:' . implode(',', [
                    'shipments.*.courier',
                    'shipments.*.tracking_number',
                    'shipments.*.status',
                    'shipments.*.shipped_at',
                    'shipments.*.delivered_at',
                    'shipments.*.remark',
                ]),
            ],
            'shipments.*.courier' => [
                'filled',
                'string',
                'max:100',
            ],
            'shipments.*.tracking_number' => [
                'filled',
                'string',
                'max:100',
            ],
            'shipments.*.status' => [
                'filled',
                'integer',
                'min:0',
            ],
            'shipments.*.shipped_at' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
            ],
            'shipments.*.delivered_at' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
            ],
            'shipments.*.remark' => [
                'filled',
                'string',
                'max:400',
            ],
            // 訂單項目
            'shipments.*.items' => [
                'filled',
                'array',
            ],
            'shipments.*.items.*.product_id' => [
                'required_with:shipments.*.items.*.quantity',
                'filled',
                'integer',
                'min:1',
            ],
            'shipments.*.items.*.quantity' => [
                'required_with:shipments.*.items.*.product_id',
                'filled',
                'integer',
                'min:0',
            ],
        ];
    }
}
