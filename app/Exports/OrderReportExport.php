<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class OrderReportExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Order::with(['client', 'driver'])
            ->get()
            ->map(function ($order) {
                return [
                    'ID'         => $order->id,
                    'Client'     => $order->client->full_name ?? '',
                    'Driver'     => $order->driver->full_name ?? '',
                    'Status'     => is_string($order->status) ? $order->status : ($order->status->value ?? ''),
                    'Passengers' => $order->passengers,
                    'Price'      => $order->price_order,
                    'Created At' => optional($order->created_at)->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Client',
            'Driver',
            'Status',
            'Passengers',
            'Price',
            'Created At',
        ];
    }
}