<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserReportExport implements FromCollection, WithHeadings
{
    public function __construct(protected Collection $users) {}

    public function collection()
    {
        return $this->users->map(function ($user) {
            return [
                'ID'          => $user->id,
                'Role'        => $user->role,
                'Email'       => $user->email,
                'Telegram ID' => $user->telegram_id,
                'Created At'  => $user->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Role',
            'Email',
            'Telegram ID',
            'Created At',
        ];
    }
}