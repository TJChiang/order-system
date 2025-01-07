<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Order\ChannelEnum;

class HktvmallGenerator extends Generator
{
    protected $channel = ChannelEnum::HKTVMALL;
}
