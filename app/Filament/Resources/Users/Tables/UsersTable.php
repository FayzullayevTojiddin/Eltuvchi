<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'client'        => 'Mijoz',
                        'driver'        => 'Haydovchi',
                        'taxoparkadmin' => 'TaxoPark Admini',
                        'superadmin'    => 'SuperAdmin',
                        default         => ucfirst($state),
                    })
                    ->badge()
                    ->colors([
                        'success' => 'client',
                        'info'    => 'driver',
                        'warning' => 'taxoparkadmin',
                        'danger'  => 'superadmin',
                    ])
                    ->sortable(),
                TextColumn::make('telegram_id')
                    ->searchable(),
                TextColumn::make('promo_code')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label("Bizga qo'shildi")
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label("So'nggi yangilanish vaqti")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('client')
                    ->label('Mijozlar')
                    ->query(fn ($query) => $query->where('role', 'client')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([  
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
