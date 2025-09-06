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
            ->label('Add History')
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
                $record->update([
                    'status' => $data['status'],
                ]);
                OrderHistory::create([
                    'order_id'       => $record->id,
                    'status'         => $data['status'],
                    'description'    => $data['description'] ?? null,
                    'changed_by_id'  => Auth::id(),
                    'changed_by_type'=> Auth::user()::class,
                ]);
            })->button();
    }
}