<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use InvalidArgumentException;

class ReportService
{
    public function generate(ReportDTO $dto): array
    {
        return match ($dto->type) {
            'orders'    => (new OrderReportService())->generate($dto),
            'users'     => (new UserReportService())->generate($dto),
            'payments'  => (new PaymentReportService())->generate($dto),
            'analytics' => (new AnalyticsReportService())->generate($dto),
            default     => throw new InvalidArgumentException('Invalid report type'),
        };
    }
}
