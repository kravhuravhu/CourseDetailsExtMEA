<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Personnel\PersonnelController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Authentication routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/integration', [DashboardController::class, 'integration'])->name('dashboard.integration');
    Route::get('/dashboard/personnel', [DashboardController::class, 'personnel'])->name('dashboard.personnel');
    Route::get('/dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');

    // Personnel Management
    Route::prefix('personnel')->name('personnel.')->group(function () {
        Route::get('/', [PersonnelController::class, 'index'])->name('index');
        Route::get('/create', [PersonnelController::class, 'create'])->name('create');
        Route::post('/', [PersonnelController::class, 'store'])->name('store');
        Route::get('/{id}', [PersonnelController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PersonnelController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PersonnelController::class, 'update'])->name('update');
        Route::delete('/{id}', [PersonnelController::class, 'destroy'])->name('destroy');
        Route::get('/export', [PersonnelController::class, 'export'])->name('export');
    });

    // Integration Logs
    Route::prefix('integration-logs')->name('integration-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Integration\IntegrationLogController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Web\Integration\IntegrationLogController::class, 'show'])->name('show');
        Route::post('/{id}/retry', [\App\Http\Controllers\Web\Integration\IntegrationLogController::class, 'retry'])->name('retry');
        Route::delete('/{id}', [\App\Http\Controllers\Web\Integration\IntegrationLogController::class, 'destroy'])->name('destroy');
        Route::get('/export', [\App\Http\Controllers\Web\Integration\IntegrationLogController::class, 'export'])->name('export');
    });

    // Organisations
    Route::prefix('organisations')->name('organisations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Web\Organisations\OrganisationController::class, 'destroy'])->name('destroy');
    });

    // Locations
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Locations\LocationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\Locations\LocationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Web\Locations\LocationController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Web\Locations\LocationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Web\Locations\LocationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Web\Locations\LocationController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Web\Locations\LocationController::class, 'destroy'])->name('destroy');
    });
});