<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetListResource extends JsonResource
{
    /** @var Order $resource */
    public $resource;

    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
