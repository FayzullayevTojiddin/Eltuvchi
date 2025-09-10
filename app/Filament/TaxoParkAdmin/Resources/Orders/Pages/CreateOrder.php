<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Pages;

use App\Filament\TaxoParkAdmin\Resources\Orders\OrderResource;
use App\Models\Route;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = "Buyurtma Yaratish";

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        $route = Route::find($data['route_id']);
        $user = Auth::user();

        if ($user->role === 'taxoparkadmin' && $route->taxopark_from_id !== $user->dispatcher->taxopark_id) {
            abort(403, 'Siz bu route uchun order yaratolmaysiz.');
        }
    }
}
