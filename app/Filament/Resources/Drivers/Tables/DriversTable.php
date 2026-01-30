<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('details.full_name')
                    ->label("To'liq nomi")
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Faol',
                        'inactive' => 'Bloklangan',
                        'new'      => 'Kutilmoqda',
                        'verify' => "Tasdiqlangan",
                    })
                    ->colors([
                        'success' => fn ($state) => $state === 'active',
                        'danger'  => fn ($state) => $state === 'inactive',
                        'warning' => fn ($state) => $state === 'new',
                        'warning' => fn($state) => $state === 'verify'
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
                SelectFilter::make('status')
                    ->label('Statusga ')
                    ->options([
                        'new'      => 'Yangi',
                        'active'   => 'Faol',
                        'inactive' => 'Bloklangan',
                        'verify' => "Tasdiqlanishi kutilayotgan",
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    DisActiveAction::create()->button(),
                    EditAction::make()->label("Tahrirlash")->button(),
                    SendMessageAction::create()
                ])
            ])
            ->toolbarActions([
                //
            ]);
    }
}
