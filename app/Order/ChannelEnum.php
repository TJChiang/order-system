<?php

namespace App\Order;

enum ChannelEnum: string
{
    case OFFICIAL = 'official';
    case MOMO = 'momo';
    case AMAZON = 'amazon';
    case HKTVMALL = 'hktvmall';
    // case SHOPEE = 'shopee';
    // case PCHOME = 'pchome';
    // case OTHER = 'other';

    public static function getValueArray(): array
    {
        return array_map(fn($channel) => $channel->value, self::cases());
    }
}
