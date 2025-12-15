<?php

namespace App\Http\Controllers;

use App\Models\{
    PayrollPeriod,
    PayrollSummary
};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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

    public function getPayrollPeriodByEmployeeId(int $employee_id = null)
    {
        
        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        if($employee_id == 0 || $employee_id == null){
            $employee_id = $user->employee->id;
        }

        $periodIds = PayrollSummary::where('employee_id', $employee_id)
            ->distinct()
            ->pluck('payroll_period_id');

        $payroll_periods = PayrollPeriod::orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->whereIn('id', $periodIds)
            ->get()
            ->map(function ($period) {
                $month = str_pad($period->period_month, 2, '0', STR_PAD_LEFT);

                $start = $period->start_date ? $period->start_date->format('d M Y') : null;
                $end   = $period->end_date   ? $period->end_date->format('d M Y')   : null;

                return [
                    'id'           => $period->id,
                    'period_year'  => $period->period_year,
                    'period_month' => $month,
                    'start_date'   => $start,
                    'end_date'     => $end,
                    'status'       => $period->status,
                    'label'        => $period->period_year . ' / ' . $month,
                    'range'        => ($start && $end) ? ($start . ' – ' . $end) : null,
                ];
            });

        return response()->json([
            'payroll_periods' => $payroll_periods,
        ]);

    }

}
