<?php

namespace App\Http\Controllers;

use App\Models\{
    Employee,
    PayrollPeriod,
    PayrollSummary,
    EmployeeEmployment,
    Shift
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
                    'judul'  => $period->judul_periode,
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
                    'judul'  => $period->judul_periode,
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

    public function getFilterPerusahaanDanJabatan(Request $request){
        $perusahaan = EmployeeEmployment::whereNotIn('employee_id', [1,2])->distinct()
            ->pluck('perusahaan');

        $penempatan = EmployeeEmployment::whereNotIn('employee_id', [1,2])->distinct()
            ->pluck('penempatan');
            
        return response()->json([
            'perusahaan' => $perusahaan,
            'position' => $penempatan,
        ]);
    }

    
    public function getPerusahaanTerakhir($employeeId)
    {
        $history = EmployeeEmployment::query()
            ->with('perusahaanModel')
            ->where('employee_id', $employeeId)
            ->orderByRaw("CASE WHEN tgl_akhir_kerja IS NULL OR tgl_akhir_kerja = '' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->firstOrFail();

        // Ambil divisi dari penempatan (harus match perusahaan_id + nama_divisi)
        $divisi = null;
        if ($history->perusahaanModel && $history->penempatan) {
            $divisi = $history->perusahaanModel->divisi()
                ->where('nama_divisi', $history->penempatan)
                ->first();
        }

        // ✅ Ambil shift dari employee
        $employee = Employee::with('shift')->find($employeeId);
        $shift = $employee->shift ?? null;

        // ✅ Format shift info
        $shiftInfo = null;
        if ($shift) {
            $shiftInfo = [
                'id' => $shift->id,
                'nama_shift' => $shift->nama_shift,
                'kode_shift' => $shift->kode_shift,
                'jam_masuk' => $shift->jam_masuk,
                'jam_pulang' => $shift->jam_pulang,
                'jam_masuk_format' => \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i'),
                'jam_pulang_format' => \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i'),
                'toleransi_keterlambatan' => $shift->toleransi_keterlambatan,
                'durasi_kerja' => $shift->durasi_kerja,
                'durasi_kerja_format' => $this->formatDurasiKerja($shift->durasi_kerja),
                'keterangan' => $shift->keterangan,
            ];
        }

        return response()->json([
            'history' => $history,
            'perusahaan' => $history->perusahaanModel,
            'divisi' => $divisi,
            'shift' => $shiftInfo, // ✅ NEW
            'employee' => [
                'id' => $employee->id,
                'nama' => $employee->nama ?? $employee->name,
                'shift_id' => $employee->shift_id,
            ],
        ]);
    }

    /**
     * Format durasi kerja dari menit ke jam:menit
     */
    private function formatDurasiKerja($menit)
    {
        if (!$menit) return '0 jam 0 menit';
        
        $jam = floor($menit / 60);
        $sisaMenit = $menit % 60;
        
        return $jam . ' jam ' . $sisaMenit . ' menit';
    }

    public function getShiftOptions(Request $request)
    {
        $activeOnly = $request->has('active_only')
            ? (string) $request->active_only !== '0'
            : true;

        $query = Shift::query()->select([
            'id',
            'nama_shift',
            'kode_shift',
            'jam_masuk',
            'jam_pulang',
            'toleransi_keterlambatan',
            'durasi_kerja',
            'is_active',
            'keterangan',
            'created_at',
            'updated_at',
        ]);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('nama_shift', 'like', "%{$q}%")
                  ->orWhere('kode_shift', 'like', "%{$q}%");
            });
        }

        $data = $query->orderBy('nama_shift')->get();

        return response()->json([
            'data' => $data,
        ]);
    }
}
