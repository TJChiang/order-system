<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Order\ChannelEnum;
use Illuminate\Validation\Rule;

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
            'start_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'before:end_time',
            ],
            'end_time' => [
                'filled',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:start_time',
            ],
            'status' => [
                'filled',
                'integer',
                'min:0',
                'max:10',
            ],
            'channel' => [
                'filled',
                'string',
                Rule::enum(ChannelEnum::class),
            ],
            'order_number' => [
                'filled',
                'string',
                'max:100',
            ],
        ];
    }
}
