<?php

namespace App\Order\Generator;

use App\Order\ChannelEnum;

class OfficialGenerator extends Generator
{
    protected ChannelEnum $channel = ChannelEnum::OFFICIAL;
}
