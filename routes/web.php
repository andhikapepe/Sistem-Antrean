<?php

use App\Livewire\Customer\DisplayAntrean;
use App\Livewire\Customer\DisplayLauncher;
use App\Livewire\Customer\Kiosk;
use App\Livewire\Dashboard;
use App\Livewire\Service\CallConsole;
use App\Livewire\Service\SelectUnit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect()->route('display.launcher');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
})->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Halaman Pilihan Loket
    Route::get('/service/select-unit', SelectUnit::class)->name('select-unit');

    // 2. Halaman Konsol dengan Slug
    Route::get('/service/call/{unit:slug}', CallConsole::class)->name('call-console');
});

// Route untuk Kiosk (Ambil Antrean)
Route::get('/kiosk', Kiosk::class)->name('kiosk');

// Panel Kontrol untuk Admin/Petugas
Route::get('/display-launcher', DisplayLauncher::class)->name('display.launcher');

// Halaman Publik untuk TV (Gunakan parameter {ids?})
Route::get('/display-antrean', DisplayAntrean::class)->name('customer.display');

require __DIR__ . '/settings.php';
require __DIR__ . '/references.php';
