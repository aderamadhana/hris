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
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 100)); // batasi biar gak dibomb per_page gede
        $page = (int) $request->get('page', 1);

        $query = PayrollPeriod::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_periode', 'like', "%{$search}%")
                ->orWhere('period_year', 'like', "%{$search}%")
                ->orWhere('period_month', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter open (periode sedang berjalan HARI INI)
        // open=1/true -> start_date <= today <= end_date
        // open=0/false -> kebalikannya
        if ($request->filled('open')) {
            $open = filter_var($request->open, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($open !== null) {
                $today = Carbon::today()->toDateString();

                if ($open) {
                    $query->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                } else {
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('start_date', '>', $today)
                        ->orWhereDate('end_date', '<', $today);
                    });
                }
            }
        }

        // Filter tanggal_mulai & tanggal_selesai (overlap range)
        // Ambil periode yang beririsan dengan [tanggal_mulai .. tanggal_selesai]
        $startStr = $request->get('tanggal_mulai');
        $endStr   = $request->get('tanggal_selesai');

        if ($startStr || $endStr) {
            try {
                $start = $startStr ? Carbon::createFromFormat('Y-m-d', $startStr)->startOfDay() : null;
                $end   = $endStr   ? Carbon::createFromFormat('Y-m-d', $endStr)->endOfDay()   : null;

                // kalau user kebalik ngisi (mulai > selesai), swap
                if ($start && $end && $start->gt($end)) {
                    [$start, $end] = [$end, $start];
                }

                if ($start && $end) {
                    $query->whereDate('start_date', '<=', $end->toDateString())
                        ->whereDate('end_date', '>=', $start->toDateString());
                } elseif ($start) {
                    $query->whereDate('end_date', '>=', $start->toDateString());
                } elseif ($end) {
                    $query->whereDate('start_date', '<=', $end->toDateString());
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Format tanggal_mulai/tanggal_selesai harus Y-m-d (contoh: 2026-01-13).',
                ], 422);
            }
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
                'start_date' => $p->start_date ? Carbon::parse($p->start_date)->format('d/m/Y') : '-',
                'end_date' => $p->end_date ? Carbon::parse($p->end_date)->format('d/m/Y') : '-',
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
                'open' => $request->open ?? '',
                'tanggal_mulai' => $request->tanggal_mulai ?? '',
                'tanggal_selesai' => $request->tanggal_selesai ?? '',
            ],
        ]);
    }
    public function create()
    {
        return Inertia::render('admin/hr/payroll/add-payroll');
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
            ->to('/hr/payroll')
            ->with('success', 'Payroll period berhasil disimpan');
    }
        

    public function edit(PayrollPeriod $payrollPeriod)
    {
        return Inertia::render('admin/hr/payroll/edit-payroll', [
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
            ->to('/hr/payroll')
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