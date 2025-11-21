<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\API\QuickEntryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/quick-entry', [DashboardController::class, 'quickEntry'])->name('dashboard.quick-entry');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Vehicle routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('vehicles', VehicleController::class);
});

// Incident routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('incidents', IncidentController::class);
});

// Transaction routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transactions', TransactionController::class);
});

// API routes for AJAX
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/vehicles/search', [QuickEntryController::class, 'searchVehicles'])->name('api.vehicles.search');
    Route::get('/vehicles/{id}', [QuickEntryController::class, 'getVehicle'])->name('api.vehicles.get');
    Route::get('/patients/search', [QuickEntryController::class, 'searchPatients'])->name('api.patients.search');
    Route::get('/patients/{id}', [QuickEntryController::class, 'getPatient'])->name('api.patients.get');
    Route::get('/stats', [QuickEntryController::class, 'getStats'])->name('api.stats');
});

require __DIR__.'/auth.php';
