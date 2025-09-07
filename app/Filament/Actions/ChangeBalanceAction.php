<?php

namespace App\Filament\Actions;

use App\Models\BalanceHistory;
use App\Models\PointHistory;
use Auth;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

class ChangeBalanceAction
{
    public static function create(): Action
    {
        return Action::make('changeBalance')
            ->label('Balans / Ball o‘zgartirish')
            ->icon('heroicon-o-currency-dollar')
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('type')
                            ->label('Turi')
                            ->options([
                                'balance' => 'Pul',
                                'points'  => 'Ball',
                            ])
                            ->required(),
                        Select::make('operation')
                            ->label('Amal')
                            ->options([
                                'plus'  => 'Qo‘shish',
                                'minus' => 'Ayirish',
                            ])
                            ->required(),
                        TextInput::make('value')
                            ->label('Qiymat')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Textarea::make('description')
                            ->required()
                            ->label('Izoh')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ])
            ])
            ->action(function ($record, array $data) {
                $type      = $data['type'];
                $operation = $data['operation'];
                $value     = $data['value'];
                $description = $data['description'] ?? null;
                if ($type === 'balance') {
                    $balanceable = $record;
                    $currentBalance = $balanceable->balance ?? 0;
                    $newBalance = $operation === 'plus'
                        ? $currentBalance + $value
                        : max(0, $currentBalance - $value);
                    $balanceable->update(['balance' => $newBalance]);
                    BalanceHistory::create([
                        'user_id' => Auth::id(),
                        'balanceable_id' => $balanceable->id,
                        'balanceable_type' => get_class($balanceable),
                        'amount' => $value,
                        'type' => $operation,
                        'balance_after' => $newBalance,
                        'description' => $description,
                    ]);
                } elseif ($type === 'points') {
                    $pointable = $record;
                    $currentPoints = $pointable->points ?? 0;
                    $newPoints = $operation === 'plus'
                        ? $currentPoints + $value
                        : max(0, $currentPoints - $value);
                    $pointable->update(['points' => $newPoints]);
                    PointHistory::create([
                        'user_id' => Auth::id(),
                        'pointable_id' => $pointable->id,
                        'pointable_type' => get_class($pointable),
                        'points' => $value,
                        'type' => $operation,
                        'points_after' => $newPoints,
                        'description' => $description,
                    ]);
                }
            })
            ->requiresConfirmation()
            ->successNotificationTitle("Muvaffaqiyatli O'tqazildi")
            ->button();
    }
}