<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class ShiftController extends Controller
{
   public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $page = (int) $request->get('page', 1);

        $query = Shift::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_shift', 'like', "%{$search}%")
                ->orWhere('kode_shift', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = filter_var(
                $request->status,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($status !== null) {
                $query->where('is_active', $status);
            }
        }

        $query->orderBy('nama_shift');

        $shifts = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $shifts->getCollection()->map(function ($s) {
            return [
                'id' => $s->id,
                'nama_shift' => $s->nama_shift,
                'kode_shift' => $s->kode_shift,
                'jam_masuk' => substr($s->jam_masuk, 0, 5),
                'jam_pulang' => substr($s->jam_pulang, 0, 5),
                'durasi_kerja' => $s->durasi_kerja,
                'toleransi_keterlambatan' => $s->toleransi_keterlambatan,
                'is_active' => ($s->is_active == true)?'Active': 'Non Active',
                'keterangan' => $s->keterangan,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $shifts->total(),
                'per_page' => $shifts->perPage(),
                'current_page' => $shifts->currentPage(),
                'last_page' => $shifts->lastPage(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }
    
    public function getData(int $id)
    {
        $period = Shift::findOrFail($id);

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
        $validated = $request->validate([
            'nama_shift' => 'required|string|max:100',
            'kode_shift' => 'required|string|max:20|unique:shift,kode_shift',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'toleransi_keterlambatan' => 'nullable|integer|min:0',
            'durasi_kerja' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        Shift::create($validated);

        return redirect()->back()->with('success', 'Shift berhasil disimpan');
    }
        

    public function edit(Shift $payrollPeriod)
    {
        return Inertia::render('admin/hr/payroll/edit-payroll', [
            'period' => $payrollPeriod
        ]);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);
        
        $validated = $request->validate([
            'nama_shift' => 'required|string|max:100',
            'kode_shift' => 'required|string|max:20|unique:shift,kode_shift,' . $shift->id,
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'toleransi_keterlambatan' => 'nullable|integer|min:0',
            'durasi_kerja' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'Shift berhasil diupdate');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();
        return redirect()->back()->with('success', 'Shift berhasil dihapus');
    }
}