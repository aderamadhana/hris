<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\LoginController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Authenticated routes (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    
    // Contoh route yang dilindungi
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

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

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


require __DIR__.'/settings.php';
