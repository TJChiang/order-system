<?php

namespace App\Order\Generator;

use App\Order\ChannelEnum;

class OfficialGenerator extends Generator
{
    protected $channel = ChannelEnum::OFFICIAL;
}
