<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\AdditionalServiceController;
use App\Http\Controllers\MaintenanceServiceController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WageTypeController;
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
    Route::get('incidents/search', [IncidentController::class, 'search'])->name('incidents.search');
    Route::resource('incidents', IncidentController::class);
});

// Transaction routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('transactions/incident/{incident}', [TransactionController::class, 'destroyByIncident'])->name('transactions.destroyByIncident');
    Route::post('transactions/distribute-dividend', [TransactionController::class, 'distributeDividend'])->name('transactions.distribute-dividend')->middleware('permission:manage settings');
    Route::resource('transactions', TransactionController::class);
});

// Patient routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('patients', PatientController::class);
});

// Report routes
Route::middleware(['auth', 'verified', 'permission:view reports'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    Route::middleware('permission:export reports')->group(function () {
        Route::get('/reports/export/incidents/excel', [ReportController::class, 'exportIncidentsExcel'])->name('reports.export.incidents.excel');
        Route::get('/reports/export/incidents/pdf', [ReportController::class, 'exportIncidentsPdf'])->name('reports.export.incidents.pdf');
        Route::get('/reports/export/transactions/excel', [ReportController::class, 'exportTransactionsExcel'])->name('reports.export.transactions.excel');
        Route::get('/reports/export/transactions/pdf', [ReportController::class, 'exportTransactionsPdf'])->name('reports.export.transactions.pdf');
        Route::get('/reports/export/vehicles/excel', [ReportController::class, 'exportVehicleReportExcel'])->name('reports.export.vehicles.excel');
    });
});

// Note routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
});

// Activity Log routes
Route::middleware(['auth', 'verified', 'permission:view audits'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Global Search routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/search', [GlobalSearchController::class, 'index'])->name('search.index');
    Route::get('/api/search', [GlobalSearchController::class, 'api'])->name('search.api');
});

// Location routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('locations', LocationController::class);
});

// Partner routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('partners', PartnerController::class);
});

// Additional Service routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('additional-services', AdditionalServiceController::class);
});

// Maintenance Service routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('maintenance-services', MaintenanceServiceController::class);
});

// Vehicle Maintenance routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('vehicle-maintenances/search/services', [VehicleMaintenanceController::class, 'searchServices'])->name('vehicle-maintenances.search.services');
    Route::get('vehicle-maintenances/search/partners', [VehicleMaintenanceController::class, 'searchPartners'])->name('vehicle-maintenances.search.partners');
    Route::resource('vehicle-maintenances', VehicleMaintenanceController::class);
});

// Staff routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('staff/payroll', [StaffController::class, 'payroll'])->name('staff.payroll')->middleware('permission:view staff');
    Route::get('staff/payroll/{year}/{month}', [StaffController::class, 'payrollDetail'])->name('staff.payroll.detail')->middleware('permission:view staff');
    Route::get('staff/{staff}/earnings', [StaffController::class, 'earnings'])->name('staff.earnings');
    Route::post('staff/{staff}/adjustments', [StaffController::class, 'storeAdjustment'])->name('staff.adjustments.store')->middleware('permission:manage settings');
    Route::put('adjustment/{adjustment}', [StaffController::class, 'updateAdjustment'])->name('adjustment.update')->middleware('permission:manage settings');
    Route::delete('adjustment/{adjustment}', [StaffController::class, 'destroyAdjustment'])->name('adjustment.destroy')->middleware('permission:manage settings');
    Route::post('staff/{staff}/salary-advance', [StaffController::class, 'storeSalaryAdvance'])->name('staff.salary-advance.store')->middleware('permission:manage settings');
    Route::put('salary-advance/{salaryAdvance}', [StaffController::class, 'updateSalaryAdvance'])->name('salary-advance.update')->middleware('permission:manage settings');
    Route::delete('salary-advance/{salaryAdvance}', [StaffController::class, 'destroySalaryAdvance'])->name('salary-advance.destroy')->middleware('permission:manage settings');
    Route::resource('staff', StaffController::class);
});

// Wage Type routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('wage-types', WageTypeController::class);
});

// API routes for AJAX
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/vehicles/search', [QuickEntryController::class, 'searchVehicles'])->name('api.vehicles.search');
    Route::get('/vehicles/{id}', [QuickEntryController::class, 'getVehicle'])->name('api.vehicles.get');
    Route::get('/patients/search', [QuickEntryController::class, 'searchPatients'])->name('api.patients.search');
    Route::get('/patients/{id}', [QuickEntryController::class, 'getPatient'])->name('api.patients.get');
    Route::get('/stats', [QuickEntryController::class, 'getStats'])->name('api.stats');
    
    // Autocomplete endpoints
    Route::get('/locations/autocomplete', [LocationController::class, 'autocomplete'])->name('api.locations.autocomplete');
    Route::get('/partners/autocomplete', [PartnerController::class, 'autocomplete'])->name('api.partners.autocomplete');
    Route::get('/additional-services/autocomplete', [AdditionalServiceController::class, 'autocomplete'])->name('api.additional-services.autocomplete');
    Route::get('/maintenance-services/autocomplete', [MaintenanceServiceController::class, 'autocomplete'])->name('api.maintenance-services.autocomplete');
});

require __DIR__.'/auth.php';
