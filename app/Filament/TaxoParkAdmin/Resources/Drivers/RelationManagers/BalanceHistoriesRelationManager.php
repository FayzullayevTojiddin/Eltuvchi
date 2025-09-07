<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BalanceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'balanceHistories';
    
    protected static ?string $title = 'Kirim Chiqimlar Tarixi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                TextColumn::make('user.display_name')
                    ->label('Kim Tomonidan')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) => $record->user ? "#{$record->user->id} {$state}" : null),
                TextColumn::make('balance_after')
                    ->label('Qolgan Pul'),
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
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
