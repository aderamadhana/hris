<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Masters\PayrollPeriodController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Authenticated routes (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil/{id}', [EmployeeController::class, 'profil']);
    Route::get('/change-password', [EmployeeController::class, 'changePassword']);
    Route::post('/proses-change-password', [EmployeeController::class, 'prosesChangePassword']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
    });

    Route::prefix('karyawan')->group(function () {
        Route::get('/all-karyawan', function () {
            return Inertia::render('admin/DataKaryawan');
        });
        Route::get('/tambah-karyawan', function () {
            return Inertia::render('admin/TambahKaryawan');
        });
        Route::get('/edit-karyawan/{id}', [EmployeeController::class, 'edit']);
        Route::get('/detail-karyawan/{id}', [EmployeeController::class, 'profil']);
        
        Route::get('/daftar-gaji/{id}', function ($id) {
            return Inertia::render('employee/SalarySlip', [
                'employeeId' => $id
            ]);
        });
    });

    Route::prefix('pelamar')->group(function () {
        Route::get('/all-pelamar', function () {
            return Inertia::render('admin/DataPelamar');
        });
    });
    
    Route::prefix('master')->group(function () {
        Route::prefix('payroll-period')->group(function () {
            Route::get('/all-data', function () {
                return Inertia::render('master/payroll_period/index');
            });
            Route::get('/', [PayrollPeriodController::class, 'index']);
            Route::get('/get-data/{id}', [PayrollPeriodController::class, 'getData']);
            Route::get('/create', [PayrollPeriodController::class, 'create']);
            Route::post('/store', [PayrollPeriodController::class, 'store']);
            Route::get('/{payrollPeriod}/edit', [PayrollPeriodController::class, 'edit']);
            Route::put('/update/{payrollPeriod}', [PayrollPeriodController::class, 'update']);
            Route::delete('/delete/{payrollPeriod}', [PayrollPeriodController::class, 'destroy']);
        });
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/detail_pelamar/{id}', [EmployeeController::class, 'detailPelamar']);
        Route::get('/get-data/{id}', [EmployeeController::class, 'getData']);
        Route::post('/import-karyawan', [EmployeeController::class, 'importKaryawan']);
        Route::post('/import-payslip', [EmployeeController::class, 'importPayslip']);
        Route::get('/import-log/{id}', [EmployeeController::class, 'showImportLog']);

        Route::post('/store', [EmployeeController::class, 'store']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::post('/store-edit/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
        Route::get('/employees/{id}/with-relations', [EmployeeController::class, 'showWithRelations']);
    
    });

    Route::prefix('payroll')->group(function () {
        Route::post('/import', [PayrollController::class, 'import'])
            ->name('payroll.import');
        
        Route::post('/import/sync', [PayrollController::class, 'importSync'])
            ->name('payroll.import.sync');
        
        Route::get('/import/progress/{importId}', [PayrollController::class, 'checkProgress'])
            ->name('payroll.import.progress');
        
        Route::delete('/import/cache/{importId}', [PayrollController::class, 'clearImportCache'])
            ->name('payroll.import.clear-cache');
    });

    Route::prefix('payslip')->group(function () {
        Route::get('/show/{id}/{employee_id}', [PayslipController::class, 'showPayslip']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/karyawan', function () {
            return Inertia::render('admin/DataKaryawan');
        });
        // Route::get('/karyawan/tambah', function () {
        //     return Inertia::render('admin/TambahKaryawan');
        // });

        Route::get('/users/{id}', function () {
            return Inertia::render('admin/DetailKaryawan');
        });
    });

    Route::get('/attendance', function () {
        return Inertia::render('employee/Attendance');
    });

    Route::get('/salary', function () {
        return Inertia::render('employee/SalarySlip');
    });

    if (app()->environment('local')) {

        Route::get('/util/migrate', function () {
            return view('util.migrate');
        })->name('util.migrate');

        Route::post('/run-migrate-fresh-seed', function () {
            Artisan::call('cache:table', []);
            Artisan::call('migrate:fresh', [
                '--seed'  => true,
                '--force' => true,
            ]);

            return back()
                ->with('status', 'migrate:fresh --seed berhasil dijalankan.')
                ->with('output', Artisan::output());
        })->name('util.run-migrate-fresh-seed');

        Route::get('/util/logs', function () {
            // ambil tanggal dari query ?date=YYYY-MM-DD, default: hari ini
            $date = request('date') ?: now()->format('Y-m-d');

            // sesuaikan kalau nama file kamu beda
            $path = storage_path("logs/laravel-{$date}.log");

            $exists = file_exists($path);
            $content = $exists ? file_get_contents($path) : null;

            return view('util.logs', [
                'date'    => $date,
                'exists'  => $exists,
                'content' => $content,
            ]);
        })->name('util.logs');
    }
});

Route::prefix('referensi')->group(function () {
    Route::get('/get-payroll_periods', [ReferensiController::class, 'getPayrollPeriod']);
    Route::get('/get-payroll-periods-by-employee-id/{id}', [ReferensiController::class, 'getPayrollPeriodByEmployeeId']);
});


// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


require __DIR__.'/settings.php';
