<?php

namespace App\Order\Generator;

use App\Models\Order;
use App\Order\ChannelEnum;

class MomoGenerator extends Generator
{
    protected $channel = ChannelEnum::MOMO;
}
