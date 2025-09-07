<?php

namespace App\Filament\Resources\Clients\RelationManagers;

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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'PointHistories';

    protected static ?string $title = 'Ballar Tarixi';

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
            ->columns([
                TextColumn::make('points')
                    ->label('Ballar')
                    ->sortable(),

                IconColumn::make('type')
                    ->label('Turi')
                    ->icon(fn (string $state): string => $state === 'plus' ? 'heroicon-o-arrow-up-circle' : 'heroicon-o-arrow-down-circle')
                    ->color(fn (string $state): string => $state === 'plus' ? 'success' : 'danger')
                    ->tooltip(fn (string $state): string => $state === 'plus' ? 'Kirim' : 'Chiqim'),

                TextColumn::make('user.display_name')
                    ->label('Kim Tomonidan')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) => $record->user ? "#{$record->user->id} {$state}" : null),

                TextColumn::make('points_after')
                    ->label('Qolgan Ball')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Izoh')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Qachon?')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
