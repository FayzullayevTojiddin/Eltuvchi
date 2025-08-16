<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Exports\OrderReportExport;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportService
{
    public function generate(ReportDTO $dto): array
    {
        $orders = Order::query()
            ->whereBetween('created_at', [$dto->from, $dto->to])
            ->get();

        return match ($dto->format) {
            'excel' => $this->exportExcel($orders, $dto),
            'csv'   => $this->exportCsv($orders, $dto),
            default => throw new InvalidArgumentException('Invalid format'),
        };
    }

    private function exportExcel($orders, ReportDTO $dto): array
    {
        $fileName = "orders-report-{$dto->from}-to-{$dto->to}.xlsx";
        $path = "reports/{$fileName}";

        Excel::store(new OrderReportExport($orders), $path, 'local');

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }

    private function exportCsv($orders, ReportDTO $dto): array
    {
        $fileName = "orders-report-{$dto->from}-to-{$dto->to}.csv";
        $path = "reports/{$fileName}";

        Excel::store(new \App\Exports\OrderReportExport($orders), $path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }
}