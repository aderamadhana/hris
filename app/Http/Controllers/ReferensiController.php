<?php

namespace App\Http\Controllers;

use App\Models\{
    PayrollPeriod,
};
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferensiController extends Controller
{
    public function getPayrollPeriod()
    {
        $payroll_periods = PayrollPeriod::orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get()
            ->map(function ($period) {
                return [
                    'id'           => $period->id,
                    'period_year'  => $period->period_year,
                    'period_month' => str_pad($period->period_month, 2, '0', STR_PAD_LEFT),

                    'start_date'   => $period->start_date
                        ? $period->start_date->format('d M Y')
                        : null,

                    'end_date'     => $period->end_date
                        ? $period->end_date->format('d M Y')
                        : null,

                    'status'       => $period->status,

                    // tambahan bantu frontend
                    'label'        => $period->period_year . ' / ' .
                                    str_pad($period->period_month, 2, '0'),
                    'range'        => ($period->start_date && $period->end_date)
                        ? $period->start_date->format('d M Y') .
                        ' – ' .
                        $period->end_date->format('d M Y')
                        : null,
                ];
            });

        return response()->json([
            'payroll_periods' => $payroll_periods,
        ]);

    }

}
