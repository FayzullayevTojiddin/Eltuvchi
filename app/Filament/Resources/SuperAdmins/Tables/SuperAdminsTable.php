<?php

namespace App\Filament\Resources\SuperAdmins\Tables;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuperAdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'Faol' : 'Bloklangan')
                    ->colors([
                        'success' => fn($state) => $state === 'active',
                        'danger'  => fn($state) => $state === 'inactive',
                    ]),
                TextColumn::make('created_at')
                    ->label("Qachondan Beri?")
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    EditAction::make()->label("Tahrirlash")->button(),
                    SendMessageAction::create(),
                    DisActiveAction::create()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
