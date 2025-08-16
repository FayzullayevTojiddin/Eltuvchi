<?php

namespace App\Http\Controllers;

use App\DTOs\ReportDTO;
use App\Http\Requests\ReportRequest;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function download(ReportRequest $request, ReportService $service)
    {
        $dto = new ReportDTO(
            type: $request->get('type'),
            from: $request->get('date_from'),
            to: $request->get('date_to'),
            format: $request->get('format')
        );

        $file = $service->generate($dto);

        return response()->download($file['path'], $file['name']);
    }
}