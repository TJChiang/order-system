<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Order\ChannelEnum;
use App\Rules\PhoneRule;
use DateTimeInterface;
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
                'date_format:' . DateTimeInterface::ATOM,
            ],
            'data.*.items' => [
                'required',
                'array',
            ],
            'data.*.items.*.product_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'data.*.items.*.sku' => [
                'required',
                'string',
                // 'uuid',
            ],
            'data.*.items.*.quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
