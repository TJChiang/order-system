<?php

namespace App\Order\Generator;

use App\Order\ChannelEnum;

class MomoGenerator extends Generator
{
    protected ChannelEnum $channel = ChannelEnum::MOMO;
}
