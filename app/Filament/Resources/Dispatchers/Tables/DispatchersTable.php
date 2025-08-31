<?php

namespace App\Filament\Resources\Dispatchers\Tables;

use App\Filament\Actions\SendMessageAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DispatchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label("To'liq Ismi")
                    ->searchable(),
                TextColumn::make('taxopark.name')
                    ->label("TaxoPark Nomi")
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'Faol' : 'Bloklangan')
                    ->colors([
                        'success' => fn($state) => $state === 'active',
                        'danger'  => fn($state) => $state === 'inactive',
                    ]),
                TextColumn::make('created_at')
                    ->label('Yaratilingan Vaqti')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label("So'nggi yangilangan Vaqti")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                EditAction::make()->label("Tahrirlash")->button(),
                SendMessageAction::create()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
