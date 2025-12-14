<?php

namespace App\Http\Controllers;

use App\Models\{
    Employee,
    EmployeeEmployment,
    PayrollPeriod,
    Earning,
    Deduction,
    Allowance
};
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayslipController extends Controller
{
    public function showPayslip(Request $request, int $payrollPeriodId, int $employee_id = null)
    {
        try {
            $employee = $request->user()->employee;

            if($employee_id != 0 || $employee_id != null){
                $employee = Employee::where('id', $employee_id)->first();
            }

            $period = PayrollPeriod::findOrFail($payrollPeriodId);
            $employement = EmployeeEmployment::where('employee_id', $employee->id)->first();

            $earnings = Earning::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->firstOrFail();

            $deductions = Deduction::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->firstOrFail()
                ;

            $allowances = Allowance::where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | PENDAPATAN (mapping eksplisit & lengkap)
            |--------------------------------------------------------------------------
            */
            $earningItems = collect([
                ['label' => 'Gaji Pokok', 'amount' => $earnings->gaji_pokok],
                ['label' => 'Gaji Harian', 'amount' => $earnings->gaji_jml],
                ['label' => 'Gaji Hari Libur', 'amount' => $earnings->gaji_hl],
                ['label' => 'Gaji Training', 'amount' => $earnings->gaji_train_jml],
                ['label' => 'Lembur', 'amount' => $earnings->lembur_jml],
                ['label' => 'Tunjangan', 'amount' => $earnings->tunj],
                ['label' => 'Bonus', 'amount' => $earnings->bonus],
                ['label' => 'Premi', 'amount' => $earnings->premi],
                ['label' => 'Insentif', 'amount' => $earnings->insentif],
                ['label' => 'Lain-lain', 'amount' => $earnings->lain_lain],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | POTONGAN
            |--------------------------------------------------------------------------
            */
            $deductionItems = collect([
                ['label' => 'BPJS TK', 'amount' => $deductions->pot_bpjs_tk],
                ['label' => 'BPJS Kesehatan', 'amount' => $deductions->pot_bpjs_kes],
                ['label' => 'PPh 21', 'amount' => $deductions->pot_pph21],
                ['label' => 'Koperasi', 'amount' => $deductions->pot_koperasi],
                ['label' => 'Makan', 'amount' => $deductions->pot_makan],
                ['label' => 'Seragam / APD', 'amount' => $deductions->pot_seragam],
                ['label' => 'Kasbon', 'amount' => $deductions->kasbon],
                ['label' => 'Potongan Payroll', 'amount' => abs($deductions->pot_payroll)],
                ['label' => 'Potongan Lain-lain', 'amount' => $deductions->pot_lain],
            ])->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])->filter(fn ($i) => $i['amount'] !== 0)->values();

            /*
            |--------------------------------------------------------------------------
            | TUNJANGAN
            |--------------------------------------------------------------------------
            */

            $allowanceItems = collect([
                ['label' => 'Tunjangan Sewa Motor', 'amount' => $allowances->tunj_sewa_motor],
                ['label' => 'Tunjangan BBM', 'amount' => $allowances->tunj_bbm],
                ['label' => 'Tunjangan Pulsa', 'amount' => $allowances->tunj_pulsa],
                ['label' => 'Tunjangan Penampilan', 'amount' => $allowances->tunj_penampilan],
                ['label' => 'Tunjangan Shift', 'amount' => $allowances->tunj_shift],
                ['label' => 'Tunjangan Makan', 'amount' => $allowances->tunj_makan],
                ['label' => 'Tunjangan Transport', 'amount' => $allowances->tunj_transport],
                ['label' => 'Tunjangan Kost', 'amount' => $allowances->tunj_kost],
                ['label' => 'Tunjangan Maintenance', 'amount' => $allowances->tunj_maintenance],
                ['label' => 'Tunjangan Posisi', 'amount' => $allowances->tunj_posisi],
                ['label' => 'Tunjangan Fisik', 'amount' => $allowances->tunj_fisik],
                ['label' => 'Tunjangan Loyalitas', 'amount' => $allowances->tunj_loyalitas],
                ['label' => 'Tunjangan Operator', 'amount' => $allowances->tunj_operator],
                ['label' => 'Tunjangan Jabatan', 'amount' => $allowances->tunj_jabatan],
                ['label' => 'Tunjangan Bag', 'amount' => $allowances->tunj_bag],
                ['label' => 'Tunjangan', 'amount' => $allowances->tunj],
            ])
            ->map(fn ($i) => [
                'label' => $i['label'],
                'amount' => (int) ($i['amount'] ?? 0),
            ])
            ->filter(fn ($i) => $i['amount'] !== 0)
            ->values();

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */
            $totalEarnings = $earningItems->sum('amount');
            $totalDeductions = $deductionItems->sum('amount');
            $totalAllowances = $allowanceItems->sum('amount');

            $takeHomePay = $totalEarnings + $totalAllowances - $totalDeductions;

            return response()->json([
                    'slip' => [
                        'company_name' => $employement->perusahaan,

                        'period_name' => $period->name,
                        'period_range' => $period->start_date->format('d M Y')
                            .' – '.$period->end_date->format('d M Y'),

                    'employee' => [
                        'nama' => $employee->nama,
                        'nik' => $employee->nik_kary,
                        'jabatan' => $employee->jabatan,
                        'divisi' => $employee->bagian,
                    ],

                    'payment' => [
                        'tanggal_bayar' => optional($period->paid_at)?->format('d M Y'),
                        'metode' => 'Transfer Bank',
                        'bank' => $employee->no_rek,
                        'status' => $period->paid_at ? 'paid' : 'unpaid',
                    ],

                    'earnings' => $earningItems,
                    'deductions' => $deductionItems,
                    'allowances' => $allowanceItems,

                    'total_earnings' => $totalEarnings,
                    'total_deductions' => $totalDeductions,
                    'total_allowance' => $totalAllowances,
                    'take_home_pay' => $takeHomePay,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                    'slip' => null
            ]);
        }
        
}

}
