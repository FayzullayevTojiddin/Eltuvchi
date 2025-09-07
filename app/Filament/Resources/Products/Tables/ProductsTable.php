<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Actions\DisActiveAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label("ID")
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label("Sarlavhasi"),

                TextColumn::make('points')
                    ->label("Points")
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

                // TextColumn::make('created_at')
                //     ->label('Qachondan Beri')
                //     ->dateTime('d-M-Y H:i')
                //     ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    DisActiveAction::create(),
                ])
            ])
            ->toolbarActions([
                //
            ])
            ->headerActions([
                //
            ]);
    }
}
