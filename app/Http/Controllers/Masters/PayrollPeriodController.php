<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        
        $query = PayrollPeriod::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_periode', 'like', "%{$search}%")
                  ->orWhere('period_year', 'like', "%{$search}%")
                  ->orWhere('period_month', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Order by
        $query->orderBy('period_year', 'desc')
              ->orderBy('period_month', 'desc');

        // Paginate
        $periods = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $periods->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'judul' => $p->judul_periode,
                'month' => $p->period_month,
                'year' => $p->period_year,
                'start_date' => $p->start_date
                    ? Carbon::parse($p->start_date)->format('d/m/Y')
                    : '-',
                'end_date' => $p->end_date
                    ? Carbon::parse($p->end_date)->format('d/m/Y')
                    : '-',
                'status' => $p->status,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $periods->total(),
                'per_page' => $periods->perPage(),
                'current_page' => $periods->currentPage(),
                'last_page' => $periods->lastPage(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('master/payroll_period/create');
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
        $validated = $request->validate([
            'judul_periode' => 'nullable|string|max:255',
            'period_year' => 'required|integer|min:2000|max:2100',
            'period_month' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:open,closed,processed',
        ]);

        PayrollPeriod::create($validated);

        return redirect()
            ->to('/master/payroll-period/all-data')
            ->with('success', 'Payroll period berhasil disimpan');
    }
        

    public function edit(PayrollPeriod $payrollPeriod)
    {
        return Inertia::render('master/payroll_period/edit', [
            'period' => $payrollPeriod
        ]);
    }

    public function update(Request $request, PayrollPeriod $payrollPeriod)
    {
        $validated = $request->validate([
            'judul_periode' => 'nullable|string|max:255',
            'period_year' => 'required|integer|min:2000|max:2100',
            'period_month' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:open,closed,processed',
        ]);

        $payrollPeriod->update($validated);

        return redirect()
            ->to('/master/payroll-period/all-data')
            ->with('success', 'Payroll period berhasil diupdate');
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        $payrollPeriod->delete();

        return response()->json([
            'message' => 'Periode payroll berhasil dihapus',
        ], 200);
    }

}