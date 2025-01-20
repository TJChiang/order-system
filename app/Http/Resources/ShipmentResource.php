<?php

namespace App\Http\Resources;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /** @var Shipment $resource */
    public $resource;

    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
