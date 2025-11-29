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
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\RolePermissionController;
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

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Debug route - REMOVE AFTER TESTING
Route::middleware(['auth'])->get('/debug-permissions', function () {
    $user = auth()->user();
    
    if (!$user) {
        return 'User not authenticated';
    }
    
    return [
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can_view_vehicles' => $user->can('view vehicles'),
        'can_view_incidents' => $user->can('view incidents'),
        'can_view_transactions' => $user->can('view transactions'),
        'can_view_patients' => $user->can('view patients'),
        'can_view_reports' => $user->can('view reports'),
    ];
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
    Route::get('vehicles/{vehicle}/export-maintenances-excel', [VehicleController::class, 'exportMaintenancesExcel'])->name('vehicles.export-maintenances-excel');
    Route::get('vehicles/{vehicle}/export-maintenances-pdf', [VehicleController::class, 'exportMaintenancesPdf'])->name('vehicles.export-maintenances-pdf');
    Route::resource('vehicles', VehicleController::class);
    
    // Loan routes
    Route::post('vehicles/{vehicle}/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::put('loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
    Route::post('loans/{loan}/adjust-interest', [LoanController::class, 'adjustInterest'])->name('loans.adjust-interest');
    Route::delete('loans/adjustments/{adjustment}', [LoanController::class, 'deleteAdjustment'])->name('loans.delete-adjustment');
    Route::post('loans/{loan}/pay-off', [LoanController::class, 'payOff'])->name('loans.pay-off');
    Route::post('loans/{loan}/process-repayments', [LoanController::class, 'processRepayments'])->name('loans.process-repayments');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
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
Route::middleware(['auth', 'verified', 'owner_or_permission:view reports'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Department Report Preview
    Route::get('/reports/department/preview', [ReportController::class, 'departmentPreview'])->name('reports.department.preview');
    
    Route::middleware('owner_or_permission:export reports')->group(function () {
        Route::get('/reports/export/incidents/excel', [ReportController::class, 'exportIncidentsExcel'])->name('reports.export.incidents.excel');
        Route::get('/reports/export/incidents/pdf', [ReportController::class, 'exportIncidentsPdf'])->name('reports.export.incidents.pdf');
        Route::get('/reports/export/transactions/excel', [ReportController::class, 'exportTransactionsExcel'])->name('reports.export.transactions.excel');
        Route::get('/reports/export/transactions/pdf', [ReportController::class, 'exportTransactionsPdf'])->name('reports.export.transactions.pdf');
        Route::get('/reports/export/vehicles/excel', [ReportController::class, 'exportVehicleReportExcel'])->name('reports.export.vehicles.excel');
        
        // Department Report Export with Notes
        Route::post('/reports/department/export-pdf-with-notes', [ReportController::class, 'exportDepartmentPdfWithNotes'])->name('reports.department.export-pdf-with-notes');
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
Route::middleware(['auth', 'verified', 'owner_or_permission:view audits'])->group(function () {
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
    Route::get('vehicle-maintenances/export-excel', [VehicleMaintenanceController::class, 'exportExcel'])->name('vehicle-maintenances.export-excel');
    Route::get('vehicle-maintenances/export-pdf', [VehicleMaintenanceController::class, 'exportPdf'])->name('vehicle-maintenances.export-pdf');
    Route::get('vehicle-maintenances/search/services', [VehicleMaintenanceController::class, 'searchServices'])->name('vehicle-maintenances.search.services');
    Route::get('vehicle-maintenances/search/partners', [VehicleMaintenanceController::class, 'searchPartners'])->name('vehicle-maintenances.search.partners');
    Route::resource('vehicle-maintenances', VehicleMaintenanceController::class);
});

// Staff routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('staff/payroll', [StaffController::class, 'payroll'])->name('staff.payroll')->middleware('owner_or_permission:view staff');
    Route::get('staff/payroll/{year}/{month}', [StaffController::class, 'payrollDetail'])->name('staff.payroll.detail')->middleware('owner_or_permission:view staff');
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

// System Settings routes
Route::middleware(['auth', 'verified', 'permission:manage settings'])->group(function () {
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/upload', [SystemSettingController::class, 'uploadFile'])->name('settings.upload');
    Route::post('/settings/delete-file', [SystemSettingController::class, 'deleteFile'])->name('settings.delete-file');
    Route::get('/settings/get-value', [SystemSettingController::class, 'getValue'])->name('settings.get-value');
});

// Asset Management routes
Route::middleware(['auth', 'verified', 'permission:manage settings'])->group(function () {
    Route::resource('assets', AssetController::class);
});

// Role Permission Management routes
Route::middleware(['auth', 'verified', 'permission:manage users'])->group(function () {
    Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::post('/role-permissions/toggle', [RolePermissionController::class, 'toggle'])->name('role-permissions.toggle');
    Route::put('/role-permissions/{role}', [RolePermissionController::class, 'update'])->name('role-permissions.update');
});

// Media Library routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::get('/media/{id}', [MediaController::class, 'show'])->name('media.show');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/media/{id}/download', [MediaController::class, 'download'])->name('media.download');
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
