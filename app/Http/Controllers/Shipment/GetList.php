<?php

namespace App\Http\Controllers\Shipment;

use App\Http\Requests\Shipment\GetListRequest;
use App\Http\Resources\ShipmentResource;
use App\Repositories\Contracts\ShipmentRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class GetList
{
    public function __invoke(GetListRequest $request, ShipmentRepository $shipmentRepository): JsonResource
    {
        $limit = min($request->input('limit', 50), 50);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $collection = $shipmentRepository->get(
            $request->validated(),
            ['*'],
            ['orderItems'],
            $offset,
            $limit,
        );

        return ShipmentResource::collection($collection)
            ->additional([
                'page' => $page,
                'limit' => $limit,
                'count' => $collection->count(),
            ]);
    }
}
