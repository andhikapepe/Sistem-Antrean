<?php

use App\Livewire\Admin\AccountManager;
use App\Livewire\Admin\QueueCategoryManager;
use App\Livewire\Admin\QueueMapping;
use App\Livewire\Admin\RoleManager;
use App\Livewire\Admin\ServiceUnitManager;
use App\Livewire\Admin\SettingManager;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('reference')->group(function () {
    Route::get('/general', SettingManager::class)->name('general');
    Route::get('/roles', RoleManager::class)->name('roles');
    Route::get('/users', AccountManager::class)->name('users');
    Route::get('/service-unit', ServiceUnitManager::class)->name('units');
    Route::get('/categories', QueueCategoryManager::class)->name('categories');
    Route::get('/mapping', QueueMapping::class)->name('mapping');
});
