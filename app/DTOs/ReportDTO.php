<?php

namespace App\DTOs;

class ReportDTO
{
    public function __construct(
        public string $type,
        public string $from,
        public string $to,
        public string $format
    ) {}
}