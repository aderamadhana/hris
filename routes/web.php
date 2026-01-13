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
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PresensiLogController;

use App\Http\Controllers\Masters\PayrollPeriodController;
use App\Http\Controllers\Masters\PerusahaanController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/reset-to-default', [LoginController::class, 'resetPasswordToDefault']);
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

    Route::prefix('hr')->group(function () {
        Route::prefix('karyawan')->group(function () {
            Route::get('/', function () {
                return Inertia::render('admin/hr/karyawan/all-karyawan');
            });
            Route::get('/tambah-karyawan', function () {
                return Inertia::render('admin/hr/karyawan/add-karyawan');
            });
            Route::get('/edit-karyawan/{id}', [EmployeeController::class, 'edit']);
            Route::get('/detail-karyawan/{id}', [EmployeeController::class, 'profil']);

            Route::get('/daftar-gaji/{id}', function ($id) {
                return Inertia::render('employee/SalarySlip', [
                    'employeeId' => $id
                ]);
            });
        });
        
        Route::get('/pelamar', function () {
            return Inertia::render('admin/hr/pelamar/all-pelamar');
        });
        
        Route::prefix('payroll')->group(function () {
            Route::get('/', function () {
                return Inertia::render('admin/hr/payroll/all-payroll');
            });
            
            Route::get('/all', [PayrollPeriodController::class, 'index']);
            Route::get('/create', [PayrollPeriodController::class, 'create']);
            Route::get('/{payrollPeriod}/edit', [PayrollPeriodController::class, 'edit']);
            Route::get('/get-data/{id}', [PayrollPeriodController::class, 'getData']);
            Route::post('/store', [PayrollPeriodController::class, 'store']);
            Route::put('/update/{payrollPeriod}', [PayrollPeriodController::class, 'update']);
            Route::delete('/delete/{payrollPeriod}', [PayrollPeriodController::class, 'destroy']);
        });

        Route::get('/surat-peringatan', function () {
            return Inertia::render('UnderDeveloping');
        });

        Route::get('/lowongan-kerja', function () {
            return Inertia::render('UnderDeveloping');
        });
    });

    Route::prefix('marketing')->group(function () {
        Route::prefix('client')->group(function () {
            Route::prefix('aktif')->group(function () {
                Route::get('/', function () {
                    return Inertia::render('admin/marketing/client-aktif/all-client');
                });
                Route::get('/all', [PerusahaanController::class, 'index']);
                Route::get('/edit/{id}', [PerusahaanController::class, 'edit']);
                Route::get('/get-data/{id}', [PerusahaanController::class, 'getData']);
                Route::get('/create', [PerusahaanController::class, 'create']);
                Route::get('/sync', [PerusahaanController::class, 'sync']);
                Route::post('/store', [PerusahaanController::class, 'store']);
                Route::put('/update/{id}', [PerusahaanController::class, 'update']);
            });
            Route::get('/non-aktif', function () {
                return Inertia::render('UnderDeveloping');
            });
            
        });
    });

    Route::prefix('log-data')->group(function () {
        Route::prefix('presensi')->group(function () {
            Route::get('/', function () {
                return Inertia::render('admin/hr/karyawan/all-karyawan');
            });
            Route::get('/tambah-karyawan', function () {
                return Inertia::render('admin/hr/karyawan/add-karyawan');
            });
            Route::get('/edit-karyawan/{id}', [EmployeeController::class, 'edit']);
            Route::get('/detail-karyawan/{id}', [EmployeeController::class, 'profil']);

            Route::get('/daftar-gaji/{id}', function ($id) {
                return Inertia::render('employee/SalarySlip', [
                    'employeeId' => $id
                ]);
            });
        });
        
        Route::get('/aktivitas', function () {
            return Inertia::render('admin/DataPelamar');
        });
    });

    Route::prefix('asuransi')->group(function () {
        Route::get('/bpjs-kesehatan', function () {
            return Inertia::render('UnderDeveloping');
        });
        Route::get('/bpjs-ketenagakerjaan', function () {
            return Inertia::render('UnderDeveloping');
        });
        Route::get('/kecelakaan-kerja', function () {
            return Inertia::render('UnderDeveloping');
        });
    });

    Route::get('/riwayat-kontrak', function () {
        return Inertia::render('UnderDeveloping');
    });

    Route::get('/surat-peringatan', function () {
        return Inertia::render('UnderDeveloping');
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

    Route::prefix('export')->group(function () {
        Route::get('/karyawan', [EmployeeController::class, 'downloadEmployees']);
        Route::get('/profil/{id}', [EmployeeController::class, 'downloadProfil']);
        Route::get('/payroll', [PayrollController::class, 'downloadPayroll']);
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

        Route::get('/{payrollPeriodId}/{employeeId}/payslip/export-pdf', [PayrollController::class, 'exportPayslipPdf'])
            ->name('payroll.payslip.export-pdf');
    });

    Route::prefix('payslip')->group(function () {
        Route::get('/show/{id}/{employee_id}', [PayslipController::class, 'showPayslip']);
    });

    
    Route::prefix('presensi')->group(function () {
        Route::post('/store', [PresensiController::class, 'store']);
        Route::get('/log-harian', [PresensiController::class, 'logHarian']);
        Route::get('/riwayat', [PresensiController::class, 'riwayat']);
        Route::post('/export', [PresensiController::class, 'export']);
    });

    Route::prefix('presensi')->group(function () {
        Route::get('/log', [PresensiLogController::class, 'logHarian']);
        Route::get('/kontrak-summary', [PresensiLogController::class, 'summary']);
    });

    //ADMIN VIEW
    Route::prefix('logs')->group(function () {
        Route::get('/presensi', function () {
            return Inertia::render('presensi/all-presensi');
        });
        Route::get('/presensi/all', [PresensiLogController::class, 'index']);

        
        Route::get('/aktivitas', function () {
            return Inertia::render('UnderDeveloping');
        });
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
    Route::get('/get-filter_perusahaan_dan_jabatan', [ReferensiController::class, 'getFilterPerusahaanDanJabatan']);
    Route::get('/get-payroll-periods-by-employee-id/{id}', [ReferensiController::class, 'getPayrollPeriodByEmployeeId']);
    Route::get('/perusahaan-terakhir/{employeeId}', [ReferensiController::class, 'getPerusahaanTerakhir']);
    Route::get('/perusahaan-divisi', [ReferensiController::class, 'getPerusahaanDanDivisi']);
    Route::get('/get-shift-options', [ReferensiController::class, 'getShiftOptions']);
    
});


// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


require __DIR__.'/settings.php';
