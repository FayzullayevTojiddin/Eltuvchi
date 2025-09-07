<?php

namespace App\Filament\Resources\Discounts\Tables;

use App\Filament\Actions\DisActiveAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('icon')
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    EditAction::make()->label("Tahrirlash")->button(),
                    DisActiveAction::create(),
                ])
            ])
            ->toolbarActions([
                //
            ]);
    }
}
