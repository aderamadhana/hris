<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\PayslipImport;
use App\Exports\PayrollExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\{
    Employee,
    EmployeeEmployment,
    // EmployeePersonal,
    SalaryConfiguration,
    AdditionalEarning,
    OvertimeSummary,
    AttendanceSummary,
    PayrollPeriod,
    PayrollSummary,
    Earning,
    Deduction,
    Allowance,
};

class PayrollController extends Controller
{
    /**
     * Import payroll data
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'payroll_period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            // Generate unique import ID
            $importId = uniqid('import_', true);
            
            // Create import instance
            $import = new PayslipImport($request->payroll_period_id, $importId);
            
            // Start import (akan di-queue otomatis karena implements ShouldQueue)
            Excel::queueImport($import, $request->file('file'));
            
            return response()->json([
                'status' => 'processing',
                'message' => 'Import has been queued for processing',
                'import_id' => $importId,
                'check_progress_url' => route('payroll.import.progress', ['importId' => $importId])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check import progress
     */
    public function checkProgress($importId)
    {
        $progress = PayslipImport::getImportProgress($importId);
        
        // Tentukan status berdasarkan progress
        $status = 'processing';
        
        if ($progress['total'] > 0) {
            if ($progress['failed'] > 0 && $progress['success'] === 0) {
                $status = 'failed';
            } elseif ($progress['failed'] > 0) {
                $status = 'completed_with_errors';
            } else {
                $status = 'completed';
            }
        }
        
        return response()->json([
            'status' => $status,
            'data' => $progress
        ]);
    }

    /**
     * Import payroll synchronously (tanpa queue)
     * Gunakan ini untuk testing atau file kecil
     */
    public function importSync(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'payroll_period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            // Generate unique import ID
            $importId = uniqid('import_', true);
            
            // Create import instance
            $import = new PayslipImport($request->payroll_period_id, $importId);
            
            // Import langsung (synchronous)
            Excel::import($import, $request->file('file'));
            
            // Get hasil import
            $results = $import->getResults();
            
            if ($results['failed'] > 0) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Import completed with errors",
                    'data' => $results
                ], 207);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => "Successfully imported {$results['success']} records",
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear import cache
     */
    public function clearImportCache($importId)
    {
        Cache::forget("import_{$importId}_success");
        Cache::forget("import_{$importId}_failed");
        Cache::forget("import_{$importId}_errors");
        
        return response()->json([
            'status' => 'success',
            'message' => 'Import cache cleared'
        ]);
    }

    public function exportPayslipPdf(Request $request, int $payrollPeriodId, int $employee_id = null)
    {
        try {
            $employee = $request->user()->employee;
            if ($employee_id != 0 && $employee_id != null) {
                $employee = Employee::where('id', $employee_id)->first();
            }

            $period = PayrollPeriod::findOrFail($payrollPeriodId);
            $employement = EmployeeEmployment::where('employee_id', $employee->id)->first();

            // ✅ Salary Configuration (rate/konfigurasi) - ambil yang berlaku untuk periode
            $salaryConfig = SalaryConfiguration::where('employee_id', $employee->id)
                ->where('effective_date', '<=', $period->start_date)
                ->orderByDesc('effective_date')
                ->first();

            // Get all related data
            $earnings = Earning::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $deductions = Deduction::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $allowances = Allowance::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $additionalEarnings = AdditionalEarning::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $overtimeSummary = OvertimeSummary::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $attendanceSummary = AttendanceSummary::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            $payrollSummary = PayrollSummary::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | PENDAPATAN UTAMA (Earnings)
            |--------------------------------------------------------------------------
            | ✅ Gaji Pokok ambil dari salary_configurations, bukan earnings
            */
            $earningItems = collect([
                ['label' => 'Gaji Pokok', 'amount' => $salaryConfig?->gaji_pokok], // ✅ FIX
                ['label' => 'Gaji HK', 'amount' => $earnings?->gaji_hk],
                ['label' => 'Gaji HL', 'amount' => $earnings?->gaji_hl],
                ['label' => 'Gaji HR', 'amount' => $earnings?->gaji_hr],
                ['label' => 'Gaji', 'amount' => $earnings?->gaji_jml],
                ['label' => 'Gaji Training', 'amount' => $earnings?->gaji_train_jml],
                ['label' => 'Gaji Revisi', 'amount' => $earnings?->gaji_rev],
                ['label' => 'Gaji Lebih Bulan Lalu', 'amount' => $earnings?->gaji_lbh_tgl23_bulan_lalu],
                ['label' => 'Lembur Total', 'amount' => $earnings?->lembur_jml],
                ['label' => 'Lembur HK', 'amount' => $earnings?->lembur_jml_hk],
                ['label' => 'Lembur HL', 'amount' => $earnings?->lembur_jml_hl],
                ['label' => 'Lembur HR', 'amount' => $earnings?->lembur_jml_hr],
                ['label' => 'Lembur Biasa', 'amount' => $earnings?->lembur_biasa_jml],
                ['label' => 'Lembur Khusus', 'amount' => $earnings?->lembur_khusus_jml],
                ['label' => 'Lembur Kurang Bulan Lalu', 'amount' => $earnings?->lembur_kurang_bulan_lalu],
                ['label' => 'Overtime', 'amount' => $earnings?->overtime],
                ['label' => 'Fee Lembur', 'amount' => $earnings?->fee_lembur],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | TUNJANGAN (Allowances)
            |--------------------------------------------------------------------------
            */
            $allowanceItems = collect([
                ['label' => 'Tunjangan Umum', 'amount' => $allowances?->tunj],
                ['label' => 'Tunjangan Sewa Motor', 'amount' => $allowances?->tunj_sewa_motor],
                ['label' => 'Tunjangan BBM', 'amount' => $allowances?->tunj_bbm],
                ['label' => 'Tunjangan Pulsa', 'amount' => $allowances?->tunj_pulsa],
                ['label' => 'Tunjangan Penampilan', 'amount' => $allowances?->tunj_penampilan],
                ['label' => 'Tunjangan Shift', 'amount' => $allowances?->tunj_shift],
                ['label' => 'Tunjangan Makan', 'amount' => $allowances?->tunj_makan],
                ['label' => 'Tunjangan Transport', 'amount' => $allowances?->tunj_transport],
                ['label' => 'Tunjangan Kost', 'amount' => $allowances?->tunj_kost],
                ['label' => 'Tunjangan Maintenance', 'amount' => $allowances?->tunj_maintenance],
                ['label' => 'Tunjangan Posisi', 'amount' => $allowances?->tunj_posisi],
                ['label' => 'Tunjangan Fisik', 'amount' => $allowances?->tunj_fisik],
                ['label' => 'Tunjangan Loyalitas', 'amount' => $allowances?->tunj_loyalitas],
                ['label' => 'Tunjangan Operator', 'amount' => $allowances?->tunj_operator],
                ['label' => 'Tunjangan Jabatan', 'amount' => $allowances?->tunj_jabatan],
                ['label' => 'Tunjangan Bag', 'amount' => $allowances?->tunj_bag],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | PENDAPATAN TAMBAHAN (Additional Earnings)
            |--------------------------------------------------------------------------
            */
            $additionalEarningItems = collect([
                ['label' => 'Anjem', 'amount' => $additionalEarnings?->anjem_jml],
                ['label' => 'Borongan', 'amount' => $additionalEarnings?->borongan_jml],
                ['label' => 'Retase', 'amount' => $additionalEarnings?->retase],
                ['label' => 'Retase Bongkar', 'amount' => $additionalEarnings?->retase_bongkar],
                ['label' => 'Piket Biasa', 'amount' => $additionalEarnings?->piket_l_biasa],
                ['label' => 'Piket Besar', 'amount' => $additionalEarnings?->piket_l_besar],
                ['label' => 'Piket Lain', 'amount' => $additionalEarnings?->piket_l_lain],
                ['label' => 'Piket BBM', 'amount' => $additionalEarnings?->piket_bbm],
                ['label' => 'Piket Reguler', 'amount' => $additionalEarnings?->piket_reguler],
                ['label' => 'Piket Hari Raya', 'amount' => $additionalEarnings?->piket_hari_raya],
                ['label' => 'Upah Hari Nasional', 'amount' => $additionalEarnings?->upah_hr_nasional],
                ['label' => 'Upah Hari Raya', 'amount' => $additionalEarnings?->upah_hr_raya],
                ['label' => 'Lembur Hari Nasional', 'amount' => $additionalEarnings?->lmbr_hr_nasional],
                ['label' => 'Bonus', 'amount' => $additionalEarnings?->bonus],
                ['label' => 'Premi', 'amount' => $additionalEarnings?->premi],
                ['label' => 'Insentif', 'amount' => $additionalEarnings?->insentif],
                ['label' => 'Insentif Malam', 'amount' => $additionalEarnings?->insentif_malam],
                ['label' => 'Perjalanan Dinas', 'amount' => $additionalEarnings?->perdin],
                ['label' => 'Pengiriman', 'amount' => $additionalEarnings?->pengiriman],
                ['label' => 'Uang Extra', 'amount' => $additionalEarnings?->uang_extra],
                ['label' => 'Accident', 'amount' => $additionalEarnings?->accident],
                ['label' => 'Pelatihan (Gaji)', 'amount' => $additionalEarnings?->pelatihan_gaji],
                ['label' => 'Rapelan', 'amount' => $additionalEarnings?->rapelan],
                ['label' => 'Kurang Bulan Lalu', 'amount' => $additionalEarnings?->kurang_bulan_lalu],
                ['label' => 'Koreksi Gaji (+)', 'amount' => $additionalEarnings?->koreksi_gaji_plus],
                ['label' => 'Koreksi PPh21', 'amount' => $additionalEarnings?->koreksi_pph21],
                ['label' => 'Pengembalian PPh21', 'amount' => $additionalEarnings?->pengembalian_pph21],
                ['label' => 'Pembulatan', 'amount' => $additionalEarnings?->pembulatan],
                ['label' => 'Lain-lain', 'amount' => $additionalEarnings?->lain_lain],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | POTONGAN (Deductions)
            |--------------------------------------------------------------------------
            */
            $deductionItems = collect([
                ['label' => 'BPJS Ketenagakerjaan', 'amount' => $deductions?->pot_bpjs_tk],
                ['label' => 'BPJS Kesehatan', 'amount' => $deductions?->pot_bpjs_kes],
                ['label' => 'BPJS', 'amount' => $deductions?->pot_bpjs],
                ['label' => 'PPh 21', 'amount' => $deductions?->pot_pph21],
                ['label' => 'Jamsos', 'amount' => $deductions?->pot_jamsos],
                ['label' => 'Potongan Makan', 'amount' => $deductions?->pot_makan],
                ['label' => 'Koperasi', 'amount' => $deductions?->pot_koperasi],
                ['label' => 'Bonus Gantung', 'amount' => $deductions?->pot_bonus_gantung],
                ['label' => 'Jam Kerja', 'amount' => $deductions?->pot_jam_kerja],
                ['label' => 'Materai', 'amount' => $deductions?->pot_materai],
                ['label' => 'Kerusakan', 'amount' => $deductions?->pot_kerusakan],
                ['label' => 'Admin', 'amount' => $deductions?->pot_admin],
                ['label' => 'APD', 'amount' => $deductions?->pot_apd],
                ['label' => 'Alfa', 'amount' => $deductions?->pot_alfa],
                ['label' => 'SPTP', 'amount' => $deductions?->pot_sptp],
                ['label' => 'Potongan Payroll', 'amount' => abs($deductions?->pot_payroll ?? 0)],
                ['label' => 'Seragam', 'amount' => $deductions?->pot_seragam],
                ['label' => 'Tidak Masuk', 'amount' => $deductions?->pot_tdk_masuk_jml],
                ['label' => 'Tidak Finger', 'amount' => $deductions?->pot_tdk_finger],
                ['label' => 'Potongan Hari Minggu', 'amount' => $deductions?->pot_hari_mingu],
                ['label' => 'Potongan Lain', 'amount' => $deductions?->pot_lain],
                ['label' => 'Klaim', 'amount' => $deductions?->klaim],
                ['label' => 'Denda', 'amount' => $deductions?->denda],
                ['label' => 'Denda Telat Briefing', 'amount' => $deductions?->denda_telat_briefing],
                ['label' => 'Kas', 'amount' => $deductions?->kas],
                ['label' => 'Kasbon', 'amount' => $deductions?->kasbon],
                ['label' => 'Mangkir', 'amount' => $deductions?->mangkir_jml],
                ['label' => 'Terlambat', 'amount' => $deductions?->terlambat_jml],
                ['label' => 'Koreksi Gaji (-)', 'amount' => $deductions?->koreksi_gaji_minus],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | TOTAL PERHITUNGAN
            |--------------------------------------------------------------------------
            */
            $totalEarnings = $earningItems->sum('amount');
            $totalAllowances = $allowanceItems->sum('amount');
            $totalAdditionalEarnings = $additionalEarningItems->sum('amount');
            $totalDeductions = $deductionItems->sum('amount');
            $totalIncome = $totalEarnings + $totalAllowances + $totalAdditionalEarnings;
            $takeHomePay = $totalIncome - $totalDeductions;
            $grandTotal = $payrollSummary?->grand_total ?? $takeHomePay;

            /*
            |--------------------------------------------------------------------------
            | ✅ tambahan untuk PDF view:
            | - salary_configuration (rate)
            | - additional_earnings_info (anjem_jam/hari, borongan_kg)
            | - attendance tambahan (tidak masuk upah)
            |--------------------------------------------------------------------------
            */
            $salaryConfigurationInfo = $salaryConfig ? [
                'effective_date' => optional($salaryConfig->effective_date)?->format('Y-m-d'),
                'gaji_hk_rate' => $salaryConfig->gaji_hk,
                'gaji_pokok_rate' => $salaryConfig->gaji_pokok,
                'gaji_per_hari_rate' => $salaryConfig->gaji_per_hari,
                'gaji_train_hk_rate' => $salaryConfig->gaji_train_hk,
                'gaji_train_upah_per_jam_rate' => $salaryConfig->gaji_train_upah_per_jam,
                'lembur_per_hari_rate' => $salaryConfig->lembur_per_hari,
                'lembur_per_jam_rate' => $salaryConfig->lembur_per_jam,
            ] : null;

            $additionalEarningsInfo = $additionalEarnings ? [
                'anjem_jam' => $additionalEarnings->anjem_jam,
                'anjem_hari' => $additionalEarnings->anjem_hari,
                'borongan_kg' => $additionalEarnings->borongan_kg,
            ] : null;

            // Prepare data for PDF
            $data = [
                'company_name' => $employement?->perusahaan ?? 'N/A',
                'period_name' => $period->judul_periode ?? ($period->name ?? 'N/A'),
                'period_range' => $period->start_date->format('d M Y') . ' – ' . $period->end_date->format('d M Y'),
                'employee' => [
                    'nama' => $employee->nama,
                    'nik' => $employee->nrp ?? $employee->no_ktp ?? $employee->nik, // ✅ FIX
                    'jabatan' => $employement->jabatan ?? 'N/A',
                    'divisi' => $employement->penempatan ?? 'N/A',
                    'no_rek' => $employee->no_rekening ?? $employee->no_rek ?? 'N/A', // ✅ FIX
                ],
                'payment' => [
                    'tanggal_bayar' => optional($period->paid_at)?->format('d M Y') ?? '-',
                    'status' => $period->paid_at ? 'Dibayar' : 'Belum Dibayar',
                ],

                // ✅ tambahan data baru untuk ditampilkan di blade
                'salary_configuration' => $salaryConfigurationInfo,
                'additional_earnings_info' => $additionalEarningsInfo,

                'earnings' => $earningItems,
                'allowances' => $allowanceItems,
                'additional_earnings' => $additionalEarningItems,
                'deductions' => $deductionItems,

                'attendance' => $attendanceSummary ? [
                    'hadir' => $attendanceSummary->hadir,
                    'jam_kerja' => $attendanceSummary->jam_kerja,
                    'jam_hk' => $attendanceSummary->jam_hk,
                    'jam_hl' => $attendanceSummary->jam_hl,
                    'jam_hr' => $attendanceSummary->jam_hr,
                    'jumlah_hl' => $attendanceSummary->jml_hl,
                    'jumlah_hr' => $attendanceSummary->jml_hr,
                    'mangkir_hari' => $attendanceSummary->mangkir_hari,
                    'tidak_masuk_hari' => $attendanceSummary->pot_tdk_masuk_hari,
                    'tidak_masuk_upah' => $attendanceSummary->pot_tdk_masuk_upah, // ✅ ADD
                    'terlambat_hari' => $attendanceSummary->terlambat_hari,
                    'terlambat_menit' => $attendanceSummary->terlambat_menit,
                    'terlambat_jam' => $attendanceSummary->terlambat_jam,
                    'ijin_pulang' => $attendanceSummary->ijin_pulang,
                    'cuti_dibayar' => $attendanceSummary->cuti_dibayar,
                ] : null,

                'overtime' => $overtimeSummary ? [
                    'overtime_jam' => $overtimeSummary->overtime_jam,
                    'total_jam' => $overtimeSummary->lembur_jam,
                    'total_hari' => $overtimeSummary->lembur_hari,
                    'lembur_jam_biasa' => $overtimeSummary->lembur_jam_biasa,
                    'lembur_jam_khusus' => $overtimeSummary->lembur_jam_khusus,
                    'lembur_minggu_2' => $overtimeSummary->lembur_minggu_2,
                    'lembur_minggu_3' => $overtimeSummary->lembur_minggu_3,
                    'lembur_minggu_4' => $overtimeSummary->lembur_minggu_4,
                    'lembur_minggu_5' => $overtimeSummary->lembur_minggu_5,
                    'lembur_minggu_6' => $overtimeSummary->lembur_minggu_6,
                    'lembur_minggu_7' => $overtimeSummary->lembur_minggu_7,
                    'lembur_libur' => $overtimeSummary->lembur_libur,
                    'lembur_2' => $overtimeSummary->lembur_2,
                ] : null,

                'total_earnings' => $totalEarnings,
                'total_allowances' => $totalAllowances,
                'total_additional_earnings' => $totalAdditionalEarnings,
                'total_income' => $totalIncome,
                'total_deductions' => $totalDeductions,
                'take_home_pay' => $takeHomePay,
                'grand_total' => $grandTotal,
            ];

            // Generate PDF
            // return view('payroll.payslip-pdf', $data);
            $pdf = \PDF::loadView('payroll.payslip-pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            $filename = 'Slip-Gaji-' . $employee->nama . '-' . ($period->name ?? $period->judul_periode ?? $period->id) . '.pdf';

            return $pdf->download($filename);

        } catch (\Throwable $th) {
            \Log::error('Error exporting payslip PDF: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'payroll_period_id' => $payrollPeriodId,
                'employee_id' => $employee_id,
            ]);

            return response()->json([
                'error' => 'Gagal mengekspor slip gaji ke PDF',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function downloadPayroll(Request $request){
        $periode_id = $request->input('periode_id');

        return Excel::download(
            new PayrollExport($periode_id),
            'karyawan.xlsx'
        );
    }
}