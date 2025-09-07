<?php

namespace App\Filament\Actions;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Enums\OrderStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class AddOrderHistoryAction
{
    public static function make(): Action
    {
        return Action::make('addHistory')
            ->label('Yangi Tarix Yozish')
            ->icon('heroicon-o-clock')
            ->form([
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->nullable(),
            ])
            ->action(function (array $data, Order $record): void {
                $record->temp_description = $data['description'] ?? null;
                $record->update([
                    'status' => $data['status'],
                ]);
            })
            ->button();
    }
}