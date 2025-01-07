<?php

namespace App\Order\Generator;

use App\Order\ChannelEnum;

class AmazonGenerator extends Generator
{
    protected ChannelEnum $channel = ChannelEnum::AMAZON;
}
