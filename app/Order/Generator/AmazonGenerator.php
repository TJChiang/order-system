<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Order\ChannelEnum;

class AmazonGenerator extends Generator
{
    protected $channel = ChannelEnum::AMAZON;

    protected function create(array $data): Order
    {
        unset($data['user_id']);
        return parent::create($data);
    }
}
