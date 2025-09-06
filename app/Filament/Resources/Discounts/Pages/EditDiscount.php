<?php

namespace App\Filament\Resources\Discounts\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Resources\Discounts\DiscountResource;
use App\Filament\Widgets\DiscountOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDiscount extends EditRecord
{
    protected static string $resource = DiscountResource::class;

    protected static ?string $title = "Chegirmani Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            DiscountOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                DisActiveAction::create(),
            ])
        ];
    }
}
