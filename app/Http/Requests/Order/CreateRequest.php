<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Order\ChannelEnum;
use App\Rules\PhoneRule;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'channel' => [
                'string',
                'required',
                Rule::enum(ChannelEnum::class),
            ],
            'data' => [
                'required',
                'array',
                'max:50',
            ],
            // 訂單
            'data.*.order_number' => [
                'string',
                'required_unless:channel,official',
                'max:100',
            ],
            'data.*.recipient_name' => [
                'required',
                'string',
                'max:255',
            ],
            'data.*.recipient_email' => [
                'nullable',
                'string',
                'email',
                'max:255',
            ],
            'data.*.recipient_phone' => [
                'nullable',
                'string',
                'max:50',
                new PhoneRule(),
            ],
            'data.*.shipping_address' => [
                'required',
                'string',
                'max:500',
            ],
            'data.*.scheduled_shipping_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'data.*.shipping_fee' => [
                'nullable',
                'numeric',
            ],
            'data.*.discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'data.*.discount_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
            'data.*.remark' => [
                'nullable',
                'string',
            ],
            'data.*.ordered_at' => [
                'required_unless:channel,official',
                'date',
                'date_format:Y-m-d H:i:s',
            ],
            // 出貨單
            'data.*.shipments' => [
                'required',
                'array',
                'max:50',
            ],
            'data.*.shipments.*.shipment_number' => [
                'required',
                'string',
                'max:100',
            ],
            'data.*.shipments.*.courier' => [
                'required',
                'string',
                'max:100',
            ],
            'data.*.shipments.*.tracking_number' => [
                'required',
                'string',
                'max:100',
            ],
            'data.*.shipments.*.remark' => [
                'nullable',
                'string',
                'max:400',
            ],
            // 訂單項目
            'data.*.shipments.*.items' => [
                'required',
                'array',
            ],
            'data.*.shipments.*.items.*.product_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'data.*.shipments.*.items.*.sku' => [
                'required',
                'string',
                // 'uuid',
            ],
            'data.*.shipments.*.items.*.quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
