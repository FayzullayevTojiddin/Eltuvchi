<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

class DriverProductDeliveredAction
{
    public static function create(): Action
    {
        return Action::make('toggleStatus')
            ->label(fn ($record) => !$record->delivered ? 'Yetqazdik' : 'Yetqazilmagan Hali')
            ->color(fn ($record) => !$record->delivered ? 'success' : 'danger')
            ->icon(fn ($record) => $record->delivered ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update([
                    'delivered' => ! $record->delivered,
                ]);
            });
    }
}