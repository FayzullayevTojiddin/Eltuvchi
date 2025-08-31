<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Filament\Actions\DisActiveAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('details.full_name')
                    ->label("To'liq nomi")
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'Faol' : 'Bloklangan')
                    ->colors([
                        'success' => fn($state) => $state === 'active',
                        'danger'  => fn($state) => $state === 'inactive',
                    ]),

                TextColumn::make('balance')
                    ->label('Balance')
                    ->numeric()
                    ->sortable()
                    ->money('USD', true),

                TextColumn::make('points')
                    ->label('Points')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d-M-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d-M-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DisActiveAction::create()->button(),
                ViewAction::make()->label("Ko'rish")->button(),
                EditAction::make()->label("Tahrirlash")->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
