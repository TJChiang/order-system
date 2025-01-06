<?php

namespace App\Http\Requests\Order;

use App\Order\ChannelEnum;
use App\Rules\PhoneRule;
use DateTimeInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => [
                'string',
                'required',
                Rule::enum(ChannelEnum::class),
            ],
            'order_number' => [
                'string',
                'required_unless:channel,official',
                'max:100',
            ],
            'recipient_name' => [
                'required',
                'string',
                'max:255',
            ],
            'recipient_email' => [
                'nullable',
                'string',
                'email',
                'max:255',
            ],
            'recipient_phone' => [
                'nullable',
                'string',
                'max:50',
                new PhoneRule(),
            ],
            'shipping_address' => [
                'required',
                'string',
                'max:500',
            ],
            'shipping_fee' => [
                'nullable',
                'numeric',
            ],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'discount_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
            'remark' => [
                'nullable',
                'string',
            ],
            'ordered_at' => [
                'required',
                'date',
                'date_format:' . DateTimeInterface::ATOM,
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'items.*.sku' => [
                'required',
                'string',
                // 'uuid',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
