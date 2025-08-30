<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;

class BalanceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'balanceHistories';
    
    protected static ?string $title = 'Kirim Chiqimlar Tarixi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('discount_id')
                    ->relationship('discount', 'title')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                IconColumn::make('type')
                    ->label('Turi')
                    ->icon(fn (string $state): string => $state === 'plus' ? 'heroicon-o-arrow-up-circle' : 'heroicon-o-arrow-down-circle')
                    ->color(fn (string $state): string => $state === 'plus' ? 'success' : 'danger')
                    ->tooltip(fn (string $state): string => $state === 'plus' ? 'Kirim' : 'Chiqim'),
                TextColumn::make('amount')
                    ->label('Miqdor')
                    ->sortable(),
                TextColumn::make('balance_after')
                    ->label('Qolgan qoldiq'),
                TextColumn::make('description')
                    ->label('Izoh')
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label('Qachon?')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}