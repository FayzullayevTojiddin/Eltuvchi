<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $title = 'Chegirmalar';

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
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('discount.title')
                    ->label('Nomi'),
                TextColumn::make('discount.type')
                    ->label('Turi'),
                TextColumn::make('discount.value')
                    ->label('Qiymati'),
                IconColumn::make('discount.status')
                    ->label('Foydalanilmagan')
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'inactive' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'secondary',
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label("Chegirma hadya qilish"),
            ])
            ->recordActions([
                DeleteAction::make()->label('Olib tashlash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}