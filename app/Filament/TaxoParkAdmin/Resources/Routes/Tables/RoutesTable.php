<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Tables;

use App\Filament\Actions\DisActiveAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoutesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fromTaxopark.name')
                    ->label('Qayerdan')
                    ->tooltip(fn ($record) => $record->fromTaxopark?->name)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toTaxopark.name')
                    ->label('Qayerga')
                    ->tooltip(fn ($record) => $record->toTaxopark?->name)
                    ->sortable()
                    ->searchable(),

                IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'disactive' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'disactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label("Ko'rish")->button(),
                ActionGroup::make([
                    DisActiveAction::create(),
                    EditAction::make()->label("Tahrirlash")->button(),
                ]),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
