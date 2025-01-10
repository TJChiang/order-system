<?php

namespace App\Http\Controllers\Order;

use App\Repositories\Contracts\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteById
{
    public function __invoke(string $id, Request $request, OrderRepository $orderRepository): Response
    {
        $orderRepository->deleteById($id);
        return response()->noContent();
    }
}
