<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
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


require __DIR__.'/settings.php';
