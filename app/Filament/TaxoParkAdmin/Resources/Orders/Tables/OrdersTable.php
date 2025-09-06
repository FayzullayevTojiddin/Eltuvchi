<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Tables;

use App\Filament\TaxoParkAdmin\Resources\Drivers\DriverResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('route.name')
                    ->label("Yo'nalish")
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->route->name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Rejalashtirilgan Vaqt')
                    ->formatStateUsing(fn ($record) => 
                        optional($record->date)?->format('d-M-Y') . ' ' . optional($record->time)?->format('H:i')
                    )
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Holat')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'created'   => 'Yaratildi',
                        'accepted'  => 'Qabul qilindi',
                        'started'   => 'Boshlangan',
                        'stopped'   => 'To‘xtatildi',
                        'completed' => 'Tugallandi',
                        'cancelled' => 'Bekor qilindi',
                    })
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        'created'   => 'primary',
                        'accepted'  => 'info',
                        'started'   => 'warning',
                        'stopped'   => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Yangilangan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    Action::make('driver')
                        ->label("Haydovchini Ko'rish")
                        ->url(fn ($record) => $record->driver 
                            ? DriverResource::getUrl('edit', ['record' => $record->driver]) 
                            : null, 
                            shouldOpenInNewTab: true
                        )
                        ->hidden(fn ($record) => blank($record->driver))
                        ->button(),
                    EditAction::make()->label("Tahrirlash")->button()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
