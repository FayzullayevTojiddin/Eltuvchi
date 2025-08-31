<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

class DriverProductDeliveredAction
{
    public static function create(): Action
    {
        return Action::make('toggleStatus')
            ->label(fn ($record) => $record->status ? 'Yetqazildi' : 'Yetqazilmadi')
            ->color(fn ($record) => $record->status ? 'success' : 'danger')
            ->icon(fn ($record) => $record->status ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update([
                    'status' => ! $record->status,
                ]);
            });
    }
}