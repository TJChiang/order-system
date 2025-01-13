<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @var Product $resource */
    public $resource;

    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
