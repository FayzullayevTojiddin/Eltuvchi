<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

class DisActiveAction
{
    public static function create(): Action
    {
        return Action::make('toggleStatus')
            ->button()
            ->label(fn ($record) => $record->status === 'active' ? 'Bloklash' : 'Faollashtirish')
            ->color(fn ($record) => $record->status === 'active' ? 'danger' : 'success')
            ->icon(fn ($record) => $record->status === 'active' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
            ->action(function ($record) {
                $record->update([
                    'status' => $record->status === 'active' ? 'inactive' : 'active',
                ]);
            })
            ->requiresConfirmation();
    }
}