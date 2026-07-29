<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExternalFormController;
use App\Http\Controllers\FormResponseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InternalFormController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public form access
Route::get('/forms/external/{id}', [PublicFormController::class, 'showExternal'])->name('public.form.external');
Route::get('/forms/internal/{id}', [PublicFormController::class, 'showInternal'])->name('public.form.internal');
Route::post('/forms/internal/{id}/submit', [PublicFormController::class, 'submitInternal'])->name('public.form.internal.submit');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes (authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('groups', GroupController::class);
    });

    // External Forms
    Route::middleware('permission:create_form,view_form')->group(function () {
        Route::resource('external-forms', ExternalFormController::class);
    });

    // Internal Forms
    Route::middleware('permission:create_form,view_form')->group(function () {
        Route::resource('internal-forms', InternalFormController::class);
        Route::get('internal-forms/{id}/builder', [InternalFormController::class, 'builder'])->name('internal-forms.builder');
    });

    // Form Responses
    Route::middleware('permission:view_form,export_responses')->group(function () {
        Route::get('forms/{formId}/responses', [FormResponseController::class, 'index'])->name('responses.index');
        Route::get('forms/{formId}/responses/{responseId}', [FormResponseController::class, 'show'])->name('responses.show');
        Route::get('forms/{formId}/responses-export/csv', [FormResponseController::class, 'exportCsv'])->name('responses.export.csv');
        Route::get('forms/{formId}/responses-export/excel', [FormResponseController::class, 'exportExcel'])->name('responses.export.excel');
        Route::get('forms/{formId}/statistics', [FormResponseController::class, 'statistics'])->name('responses.statistics');
    });
});
