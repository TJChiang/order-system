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
            // 若無 shipments，則表示不更新；反之，必須出現該訂單的所有出貨單
            'shipments' => [
                'filled',
                'array',
                'max:50',
            ],
            'shipments.*.id' => [
                'filled',
                'integer',
            ],
            'shipments.*.shipment_number' => [
                'required_without:shipments.*.id',
                'string',
                'max:100',
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
            // 想像上品項是使用者產生的，後台應該不能變動，頂多調整要出貨的數量
            // 若無 items，則表示不更新；反之，必須出現該出貨單的所有品項
            // 若有品項，則數量必須大於 1
            'shipments.*.items' => [
                'filled',
                'array',
            ],
            'shipments.*.items.*.id' => [
                'filled',
                'integer',
                'min:1',
            ],
            'shipments.*.items.*.quantity' => [
                'required_with:shipments.*.items.*.id',
                'filled',
                'integer',
                'min:1',
            ],
        ];
    }
}
