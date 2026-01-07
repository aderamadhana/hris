<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class PerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $search  = $request->string('search')->trim();
        $status  = $request->string('status')->trim();

        $query = Perusahaan::query();

        // Search: nama / kode perusahaan
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('kode_perusahaan', 'like', "%{$search}%");
            });
        }

        // Filter status
        if (in_array($status, ['aktif', 'tidak_aktif'])) {
            $query->where('status', $status);
        }

        // Urutkan: MOU terdekat habis di atas
        $query->orderByRaw("
            CASE 
                WHEN tanggal_akhir_mou IS NULL THEN 1
                ELSE 0
            END
        ")
        ->orderBy('tanggal_akhir_mou', 'asc');

        $perusahaan = $query->paginate($perPage);

        return response()->json($perusahaan);
    }

    public function create()
    {
        return Inertia::render('master/client/add-client');
    }

    
    public function getData(int $id)
    {
        $period = PayrollPeriod::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $period->id,
                'judul_periode' => $period->judul_periode,
                'period_year' => $period->period_year,
                'period_month' => $period->period_month,
                'start_date' => $period->start_date
                    ? $period->start_date->format('Y-m-d')
                    : null,
                'end_date' => $period->end_date
                    ? $period->end_date->format('Y-m-d')
                    : null,
                'status' => $period->status,
            ],
        ]);
    }


    public function store(Request $request)
    {
        
    }
        

    public function edit(PayrollPeriod $payrollPeriod)
    {
        
    }

    public function update(Request $request, PayrollPeriod $payrollPeriod)
    {
        
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        
    }

}