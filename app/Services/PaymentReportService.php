<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Exports\PaymentReportExport;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class PaymentReportService
{
    public function generate(ReportDTO $dto): array
    {
        $payments = Payment::query()
            ->whereBetween('created_at', [$dto->from, $dto->to])
            ->get();

        return match ($dto->format) {
            'excel' => $this->exportExcel($payments, $dto),
            'csv'   => $this->exportCsv($payments, $dto),
            default => throw new InvalidArgumentException('Invalid format'),
        };
    }

    private function exportExcel($payments, ReportDTO $dto): array
    {
        $fileName = "payments-report-{$dto->from}-to-{$dto->to}.xlsx";
        $path = "reports/{$fileName}";

        Excel::store(new PaymentReportExport($payments), $path, 'local');

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }

    private function exportCsv($payments, ReportDTO $dto): array
    {
        $fileName = "payments-report-{$dto->from}-to-{$dto->to}.csv";
        $path = "reports/{$fileName}";

        Excel::store(new PaymentReportExport($payments), $path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }
}