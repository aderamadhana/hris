<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ReferensiController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Authenticated routes (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/profil/{id}', [EmployeeController::class, 'profil']);
        Route::get('/get-data/{id}', [EmployeeController::class, 'getData']);
        Route::post('/import-karyawan', [EmployeeController::class, 'importKaryawan']);
        Route::post('/import-payslip', [EmployeeController::class, 'importPayslip']);
        Route::get('/import-log/{id}', [EmployeeController::class, 'showImportLog']);
    });

    Route::prefix('payslip')->group(function () {
        Route::get('/show/{id}', [PayslipController::class, 'showPayslip']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/users', function () {
            return Inertia::render('admin/UserIndex');
        });

        Route::get('/users/{id}', function () {
            return Inertia::render('admin/UserDetail');
        });
    });

    Route::get('/attendance', function () {
        return Inertia::render('employee/Attendance');
    });

    Route::get('/salary', function () {
        return Inertia::render('employee/SalarySlip');
    });
});

Route::prefix('referensi')->group(function () {
    Route::get('/get-payroll_periods', [ReferensiController::class, 'getPayrollPeriod']);
});

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


require __DIR__.'/settings.php';
