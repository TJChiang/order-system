<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\General\DatabaseException;
use App\Http\Requests\Order\CreateRequest;
use App\Order\ChannelEnum;
use App\Order\Generator\Generator as OrderGenerator;
use App\Order\Generator\Manager as OrderGeneratorManager;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Throwable;

class Create
{
    public function __invoke(CreateRequest $request, OrderGeneratorManager $orderGeneratorManager,): Response
    {
        $channel = $request->input('channel');
        $data = $request->input('data');

        /** @var OrderGenerator $orderGenerator */
        $orderGenerator = $orderGeneratorManager->driver($channel);

        $orderData = array_map(function ($data) use ($channel) {
            if ($channel === ChannelEnum::OFFICIAL->value) {
                // mock user_id
                $data['user_id'] = 1;
                $data['order_number'] = Str::uuid()->toString();
                $data['ordered_at'] = CarbonImmutable::now();
            }
            $data['status'] = 0;
            return $data;
        }, $data);

        try {
            $orderGenerator->generate($orderData);
        } catch (Throwable $e) {
            throw (new DatabaseException('Failed to generate orders.'))
                ->setExtraError([
                    'error.class' => $e::class,
                    'error.message' => $e->getMessage(),
                    'channel' => $channel,
                ]);
        }

        return response()->noContent(201);
    }
}
