<?php

use App\Filament\Auth\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Livewire\Livewire;

Livewire::component('app.filament.auth.login', Login::class);

Route::get('/login', Login::class)->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');