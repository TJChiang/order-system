<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @var Order $resource */
    public $resource;

    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
