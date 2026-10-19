<?php

use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\JadwalPeriksaController;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// General Dashboard Redirect based on user role
Route::get('/dashboard', function () {
    $role = auth()->user()->role ?? null;
    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'dokter' => redirect()->route('dokter.dashboard'),
        'pasien' => redirect()->route('pasien.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware('auth')->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat', ObatController::class);
    Route::resource('jadwal_periksa', JadwalPeriksaController::class);
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');
});

// Pasien Routes
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function () {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');
});