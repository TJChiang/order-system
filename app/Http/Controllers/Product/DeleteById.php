<?php

namespace App\Http\Controllers\Product;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class DeleteById
{
    public function __invoke(int $id, Request $request, ProductRepository $productRepository)
    {
        $productRepository->deleteById($id);

        return response()->noContent();
    }
}
