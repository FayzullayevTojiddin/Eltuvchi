<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Exports\UserReportExport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class UserReportService
{
    public function generate(ReportDTO $dto): array
    {
        $users = User::query()
            ->whereBetween('created_at', [$dto->from, $dto->to])
            ->get();

        return match ($dto->format) {
            'excel' => $this->exportExcel($users, $dto),
            'csv'   => $this->exportCsv($users, $dto),
            default => throw new InvalidArgumentException('Invalid format'),
        };
    }

    private function exportExcel($users, ReportDTO $dto): array
    {
        $fileName = "users-report-{$dto->from}-to-{$dto->to}.xlsx";
        $path = "reports/{$fileName}";

        Excel::store(new UserReportExport($users), $path, 'local');

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }

    private function exportCsv($users, ReportDTO $dto): array
    {
        $fileName = "users-report-{$dto->from}-to-{$dto->to}.csv";
        $path = "reports/{$fileName}";

        Excel::store(new UserReportExport($users), $path, 'local', \Maatwebsite\Excel\Excel::CSV);

        return [
            'name' => $fileName,
            'path' => Storage::disk('local')->path($path),
        ];
    }
}