<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\FactionController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\UnitClassController;
use App\Http\Controllers\WeaponController;

Route::resource('factions', FactionController::class);
Route::resource('ranks', RankController::class);
Route::resource('unit-classes', UnitClassController::class);
Route::resource('weapons', WeaponController::class);

// URL Utama langsung mengarah ke Tabel Daftar Personel
Route::get('/', [PersonnelController::class, 'index'])->name('personnel.index');

// Form Create
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');

// Endpoint Submit Form
Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');

// Halaman Edit Form
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');

// Endpoint Update Data (menggunakan PUT)
Route::put('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');

// Endpoint Delete Data
Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');