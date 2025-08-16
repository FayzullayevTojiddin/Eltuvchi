<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Exports\AnalyticsReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsReportService
{
    public function generate(ReportDTO $dto): array
    {
        $analytics = collect([
            [
                'Orders Count' => DB::table('orders')
                    ->whereBetween('created_at', [$dto->from, $dto->to])
                    ->count(),
                'Users Count' => DB::table('users')
                    ->whereBetween('created_at', [$dto->from, $dto->to])
                    ->count(),
                'Payments Sum' => DB::table('payments')
                    ->whereBetween('created_at', [$dto->from, $dto->to])
                    ->sum('amount'),
            ]
        ]);

        return match ($dto->format) {
            'excel' => $this->exportExcel($analytics, $dto),
            'csv'   => $this->exportCsv($analytics, $dto),
            default => throw new InvalidArgumentException('Invalid format'),
        };
    }

    private function exportExcel($analytics, ReportDTO $dto): array
    {
        $fileName = "analytics-report-{$dto->from}-to-{$dto->to}.xlsx";
        $path = "reports/{$fileName}";

        Excel::store(new AnalyticsReportExport($analytics), $path, 'local');

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }

    private function exportCsv($analytics, ReportDTO $dto): array
    {
        $fileName = "analytics-report-{$dto->from}-to-{$dto->to}.csv";
        $path = "reports/{$fileName}";

        Excel::store(new AnalyticsReportExport($analytics), $path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }
}