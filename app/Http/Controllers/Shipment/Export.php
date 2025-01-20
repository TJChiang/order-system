<?php

namespace App\Http\Controllers\Shipment;

use App\Exceptions\General\DataNotFoundException;
use App\Http\Requests\Shipment\ExportRequest;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class Export
{
    /**
     * @throws DataNotFoundException
     */
    public function __invoke(ExportRequest $request, ShipmentRepository $shipmentRepository): Response
    {
        $shipmentNumber = $request->get('shipment_number');
        $collection = $shipmentRepository->getByShipmentNumber($shipmentNumber, with: ['orderItems', 'order']);
        if ($collection->isEmpty()) {
            throw new DataNotFoundException("Shipment with number: {$shipmentNumber} not found");
        }

        /** @var Shipment $shipment */
        $shipment = $collection->first();
        $orderItemsInfo = $this->getOrderItemInfo($shipment->orderItems);
        $viewData = [
            'shipment_number' => $shipment->shipment_number,
            'courier' => $shipment->courier ?? '',
            'tracking_number' => $shipment->tracking_number ?? '',
            'shipping_date' => $shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d H:i:s') : '',
            'delivery_date' => $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d H:i:s') : '',
            'status' => match ($shipment->status) {
                0 => '待出貨',
                1 => '已出貨',
                2 => '已送達',
                default => '異常',
            },
            'order_number' => $shipment->order->order_number,
            'shipping_address' => $shipment->order->shipping_address,
            'recipient_name' => $shipment->order->recipient_name,
            'recipient_phone' => $shipment->order->recipient_phone ?? '',
            'recipient_email' => $shipment->order->recipient_email ?? '',
            'order_date' => $shipment->order->ordered_at ? $shipment->order->ordered_at->format('Y-m-d H:i:s') : '',
            'order_items' => $orderItemsInfo['items'],
            'total_quantity' => $orderItemsInfo['total_quantity'],
            'total_price' => $orderItemsInfo['total_price'],
        ];

        $pdf = Pdf::loadView('pdf.shipments', $viewData);
        $filename = "shipment-{$shipment->shipment_number}.pdf";

        return $pdf->stream($filename);
    }

    private function getOrderItemInfo(Collection $orderItems): array
    {
        $items = [];
        $totalQuantity = 0;
        $totalPrice = 0;
        foreach ($orderItems as $item) {
            $quantity = $item->pivot->quantity;
            $subTotal = $quantity * $item->price;

            $items[] = [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $quantity,
                'price' => $item->price,
                'subtotal' => $subTotal,
            ];
            $totalQuantity += $quantity;
            $totalPrice += $subTotal;
        }

        return [
            'items' => $items,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ];
    }
}
