<?php

namespace App\Http\Controllers;

use App\Models\{
    Employee,
    EmployeeEmployment,
    AdditionalEarning,
    OvertimeSummary,
    AttendanceSummary,
    PayrollPeriod,
    PayrollSummary,
    Earning,
    Deduction,
    Allowance
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayslipController extends Controller
{
    public function showPayslip(Request $request, int $payrollPeriodId, int $employee_id = null)
    {
        try {
            $employee = $request->user()->employee;

            if ($employee_id != 0 && $employee_id != null) {
                $employee = Employee::where('id', $employee_id)->first();
            }

            $period = PayrollPeriod::findOrFail($payrollPeriodId);
            $employement = EmployeeEmployment::where('employee_id', $employee->id)->first();

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
            */
            $earningItems = collect([
                ['label' => 'Gaji Pokok', 'amount' => $earnings?->gaji_pokok],
                ['label' => 'Gaji HK', 'amount' => $earnings?->gaji_hk],
                ['label' => 'Gaji HL', 'amount' => $earnings?->gaji_hl],
                ['label' => 'Gaji HR', 'amount' => $earnings?->gaji_hr],
                ['label' => 'Jumlah Gaji', 'amount' => $earnings?->gaji_jml],
                ['label' => 'Gaji Training', 'amount' => $earnings?->gaji_train_jml],
                ['label' => 'Gaji Revisi', 'amount' => $earnings?->gaji_rev],
                ['label' => 'Gaji Lebih Bulan Lalu', 'amount' => $earnings?->gaji_lbh_tgl23_bulan_lalu],
                
                // Lembur dari Earnings
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
                // Anjem
                ['label' => 'Anjem', 'amount' => $additionalEarnings?->anjem_jml],
                
                // Borongan
                ['label' => 'Borongan', 'amount' => $additionalEarnings?->borongan_jml],
                
                // Retase
                ['label' => 'Retase', 'amount' => $additionalEarnings?->retase],
                ['label' => 'Retase Bongkar', 'amount' => $additionalEarnings?->retase_bongkar],
                
                // Piket
                ['label' => 'Piket Biasa', 'amount' => $additionalEarnings?->piket_l_biasa],
                ['label' => 'Piket Besar', 'amount' => $additionalEarnings?->piket_l_besar],
                ['label' => 'Piket Lain', 'amount' => $additionalEarnings?->piket_l_lain],
                ['label' => 'Piket BBM', 'amount' => $additionalEarnings?->piket_bbm],
                ['label' => 'Piket Reguler', 'amount' => $additionalEarnings?->piket_reguler],
                ['label' => 'Piket Hari Raya', 'amount' => $additionalEarnings?->piket_hari_raya],
                
                // Upah Hari Libur/Raya
                ['label' => 'Upah Hari Nasional', 'amount' => $additionalEarnings?->upah_hr_nasional],
                ['label' => 'Upah Hari Raya', 'amount' => $additionalEarnings?->upah_hr_raya],
                ['label' => 'Lembur Hari Nasional', 'amount' => $additionalEarnings?->lmbr_hr_nasional],
                
                // Bonus & Insentif
                ['label' => 'Bonus', 'amount' => $additionalEarnings?->bonus],
                ['label' => 'Premi', 'amount' => $additionalEarnings?->premi],
                ['label' => 'Insentif', 'amount' => $additionalEarnings?->insentif],
                ['label' => 'Insentif Malam', 'amount' => $additionalEarnings?->insentif_malam],
                
                // Lain-lain
                ['label' => 'Perjalanan Dinas', 'amount' => $additionalEarnings?->perdin],
                ['label' => 'Pengiriman', 'amount' => $additionalEarnings?->pengiriman],
                ['label' => 'Uang Extra', 'amount' => $additionalEarnings?->uang_extra],
                ['label' => 'Accident', 'amount' => $additionalEarnings?->accident],
                ['label' => 'Pelatihan (Gaji)', 'amount' => $additionalEarnings?->pelatihan_gaji],
                
                // Koreksi
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
                // BPJS & Pajak
                ['label' => 'BPJS Ketenagakerjaan', 'amount' => $deductions?->pot_bpjs_tk],
                ['label' => 'BPJS Kesehatan', 'amount' => $deductions?->pot_bpjs_kes],
                ['label' => 'BPJS', 'amount' => $deductions?->pot_bpjs],
                ['label' => 'PPh 21', 'amount' => $deductions?->pot_pph21],
                ['label' => 'Jamsos', 'amount' => $deductions?->pot_jamsos],
                
                // Potongan Umum
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
                
                // Klaim & Denda
                ['label' => 'Klaim', 'amount' => $deductions?->klaim],
                ['label' => 'Denda', 'amount' => $deductions?->denda],
                ['label' => 'Denda Telat Briefing', 'amount' => $deductions?->denda_telat_briefing],
                
                // Kas & Kasbon
                ['label' => 'Kas', 'amount' => $deductions?->kas],
                ['label' => 'Kasbon', 'amount' => $deductions?->kasbon],
                
                // Mangkir & Terlambat
                ['label' => 'Mangkir', 'amount' => $deductions?->mangkir_jml],
                ['label' => 'Terlambat', 'amount' => $deductions?->terlambat_jml],
                
                // Koreksi
                ['label' => 'Koreksi Gaji (-)', 'amount' => $deductions?->koreksi_gaji_minus],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | INFORMASI KEHADIRAN
            |--------------------------------------------------------------------------
            */
            $attendanceInfo = null;
            if ($attendanceSummary) {
                $attendanceInfo = [
                    'jam_kerja' => $attendanceSummary->jam_kerja,
                    'jam_hk' => $attendanceSummary->jam_hk,
                    'jam_hl' => $attendanceSummary->jam_hl,
                    'jam_hr' => $attendanceSummary->jam_hr,
                    'jumlah_hl' => $attendanceSummary->jml_hl,
                    'jumlah_hr' => $attendanceSummary->jml_hr,
                    'hadir' => $attendanceSummary->hadir,
                    'mangkir_hari' => $attendanceSummary->mangkir_hari,
                    'tidak_masuk_hari' => $attendanceSummary->pot_tdk_masuk_hari,
                    'terlambat_hari' => $attendanceSummary->terlambat_hari,
                    'terlambat_menit' => $attendanceSummary->terlambat_menit,
                    'terlambat_jam' => $attendanceSummary->terlambat_jam,
                    'ijin_pulang' => $attendanceSummary->ijin_pulang,
                    'cuti_dibayar' => $attendanceSummary->cuti_dibayar,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | INFORMASI LEMBUR
            |--------------------------------------------------------------------------
            */
            $overtimeInfo = null;
            if ($overtimeSummary) {
                $overtimeInfo = [
                    'overtime_jam' => $overtimeSummary->overtime_jam,
                    'lembur_hari' => $overtimeSummary->lembur_hari,
                    'lembur_jam' => $overtimeSummary->lembur_jam,
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
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL PERHITUNGAN
            |--------------------------------------------------------------------------
            */
            $totalEarnings = $earningItems->sum('amount');
            $totalAllowances = $allowanceItems->sum('amount');
            $totalAdditionalEarnings = $additionalEarningItems->sum('amount');
            $totalDeductions = $deductionItems->sum('amount');

            // Total Pendapatan = Earnings + Allowances + Additional Earnings
            $totalIncome = $totalEarnings + $totalAllowances + $totalAdditionalEarnings;
            
            // Take Home Pay = Total Pendapatan - Total Potongan
            $takeHomePay = $totalIncome - $totalDeductions;

            // Validasi dengan grand_total dari payroll_summary
            $grandTotal = $payrollSummary?->grand_total ?? $takeHomePay;

            return response()->json([
                'slip' => [
                    'company_name' => $employement?->perusahaan ?? 'N/A',

                    'period_name' => $period->name ?? 'N/A',
                    'period_range' => $period->start_date->format('d M Y')
                        . ' – ' . $period->end_date->format('d M Y'),

                    'employee' => [
                        'nama' => $employee->nama,
                        'nik' => $employee->nik_kary ?? $employee->nik,
                        'jabatan' => $employee->jabatan ?? 'N/A',
                        'divisi' => $employee->bagian ?? 'N/A',
                        'no_rek' => $employee->no_rek ?? 'N/A',
                    ],

                    'payment' => [
                        'tanggal_bayar' => optional($period->paid_at)?->format('d M Y'),
                        'metode' => 'Transfer Bank',
                        'bank' => $employee->no_rek ?? 'N/A',
                        'status' => $period->paid_at ? 'paid' : 'unpaid',
                    ],

                    // Breakdown Pendapatan
                    'earnings' => $earningItems,
                    'allowances' => $allowanceItems,
                    'additional_earnings' => $additionalEarningItems,
                    
                    // Potongan
                    'deductions' => $deductionItems,

                    // Informasi Tambahan
                    'attendance' => $attendanceInfo,
                    'overtime' => $overtimeInfo,

                    // Summary Totals
                    'total_earnings' => $totalEarnings,
                    'total_allowances' => $totalAllowances,
                    'total_additional_earnings' => $totalAdditionalEarnings,
                    'total_income' => $totalIncome,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $takeHomePay,
                    'grand_total' => $grandTotal, // Dari database
                ],
            ]);
        } catch (\Throwable $th) {
            \Log::error('Error showing payslip: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'payroll_period_id' => $payrollPeriodId,
                'employee_id' => $employee_id,
            ]);

            return response()->json([
                'slip' => null,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

}
