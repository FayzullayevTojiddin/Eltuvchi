<?php

namespace App\Filament\Resources\SuperAdmins\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SuperAdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->searchable(['id', 'email', 'telegram_id'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->email}")
                    ->createOptionForm([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('telegram_id')->required(),
                                TextInput::make('password')->password()->required(),
                                TextInput::make('email')->email()->unique()->required()->columnSpanFull(),
                            ])
                    ])
                    ->createOptionAction(function ($action) {
                        return $action
                            ->modalHeading('Yangi foydalanuvchi yaratish')
                            ->modalButton('Yaratish')
                            ->icon('heroicon-o-plus');
                    })
                    ->suffixAction(
                        Action::make('previewUser')
                            ->label('Ko‘rish')
                            ->icon('heroicon-o-eye')
                            ->modalHeading('Foydalanuvchi maʼlumotlari')
                            ->modalWidth('lg')
                            ->infolist([
                                TextEntry::make('id'),
                                TextEntry::make('email'),
                                TextEntry::make('telegram_id'),
                                TextEntry::make('role')
                            ])
                            ->record(function ($state) {
                                return User::find($state);
                            })
                            ->visible(fn ($state) => filled($state))   
                    )
                    ->required(),

                TextInput::make('full_name')
                    ->label("To'liq Ism")
                    ->required(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Bloklangan',
                    ])
                    ->disabled()
                    ->default('active'),
            ])
            ->columns(3);
    }
}
