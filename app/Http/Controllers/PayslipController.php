<?php

namespace App\Http\Controllers;

use App\Models\{
    Employee,
    PayrollPeriod,
    Earning,
    Deduction,
};
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayslipController extends Controller
{
    public function showPayslip(Request $request, int $payrollPeriodId)
    {
        $employee = $request->user()->employee;

        $period = PayrollPeriod::findOrFail($payrollPeriodId);

        $earnings = Earning::where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->firstOrFail();

        $deductions = Deduction::where('employee_id', $employee->id)
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
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $totalEarnings = $earningItems->sum('amount');
        $totalDeductions = $deductionItems->sum('amount');

        $takeHomePay = $totalEarnings - $totalDeductions;

        return response()->json([
            'slip' => [
                'company_name' => 'PT Contoh Sejahtera Abadi',

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

            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'take_home_pay' => $takeHomePay,
        ],
    ]);
}

}
