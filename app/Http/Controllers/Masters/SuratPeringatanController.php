<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\SuratPeringatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratPeringatanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $page    = (int) $request->get('page', 1);

        $query = SuratPeringatan::query()
            ->with(['employee:id,nama,no_ktp']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nomor_sp', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($e) use ($search) {
                        $e->where('nama', 'like', "%{$search}%")
                          ->orWhere('no_ktp', 'like', "%{$search}%");
                    });

                if (ctype_digit((string) $search)) {
                    $q->orWhere('employee_id', (int) $search);
                }
            });
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', (int) $request->employee_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_sp', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_sp', '<=', $request->tanggal_sampai);
        }

        $query->orderByDesc('tanggal_sp')->orderByDesc('id');

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $items->getCollection()
            ->map(fn ($sp) => $this->transform($sp))
            ->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'tingkat' => $request->tingkat ?? '',
                'employee_id' => $request->employee_id ?? '',
                'tanggal_dari' => $request->tanggal_dari ?? '',
                'tanggal_sampai' => $request->tanggal_sampai ?? '',
            ],
        ]);
    }

    public function getData(int $id)
    {
        $sp = SuratPeringatan::with(['employee:id,nama,no_ktp'])->findOrFail($id);

        return response()->json([
            'data' => $this->transform($sp),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'nomor_sp' => 'required|string|max:50|unique:surat_peringatans,nomor_sp',
            'tanggal_sp' => 'required|date',
            'tingkat' => 'required|in:SP1,SP2,SP3',
            'pelanggaran' => 'required|string',
            'tanggal_kejadian' => 'nullable|date',

            // ✅ baru
            'periode_bulan' => 'required|integer|min:1|max:36',

            'file' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $path = $request->file('file')->store('surat-peringatan', 'public');

        SuratPeringatan::create([
            'employee_id' => $validated['employee_id'],
            'nomor_sp' => $validated['nomor_sp'],
            'tanggal_sp' => $validated['tanggal_sp'],
            'tingkat' => $validated['tingkat'],
            'pelanggaran' => $validated['pelanggaran'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'] ?? null,

            // ✅ simpan periode_bulan, tanggal_berakhir dihitung oleh model
            'periode_bulan' => $validated['periode_bulan'],

            'file_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Surat peringatan berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $sp = SuratPeringatan::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'nomor_sp' => 'required|string|max:50|unique:surat_peringatans,nomor_sp,' . $sp->id,
            'tanggal_sp' => 'required|date',
            'tingkat' => 'required|in:SP1,SP2,SP3',
            'pelanggaran' => 'required|string',
            'tanggal_kejadian' => 'nullable|date',

            // ✅ baru
            'periode_bulan' => 'required|integer|min:1|max:36',

            'file' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($request->hasFile('file')) {
            if ($sp->file_path && Storage::disk('public')->exists($sp->file_path)) {
                Storage::disk('public')->delete($sp->file_path);
            }
            $sp->file_path = $request->file('file')->store('surat-peringatan', 'public');
        }

        $sp->employee_id = $validated['employee_id'];
        $sp->nomor_sp = $validated['nomor_sp'];
        $sp->tanggal_sp = $validated['tanggal_sp'];
        $sp->tingkat = $validated['tingkat'];
        $sp->pelanggaran = $validated['pelanggaran'];
        $sp->tanggal_kejadian = $validated['tanggal_kejadian'] ?? null;

        // ✅ simpan periode_bulan, tanggal_berakhir dihitung oleh model
        $sp->periode_bulan = $validated['periode_bulan'];

        $sp->save();

        return redirect()->back()->with('success', 'Surat peringatan berhasil diupdate');
    }

    public function destroy($id)
    {
        $sp = SuratPeringatan::findOrFail($id);

        if ($sp->file_path && Storage::disk('public')->exists($sp->file_path)) {
            Storage::disk('public')->delete($sp->file_path);
        }

        $sp->delete();

        return redirect()->back()->with('success', 'Surat peringatan berhasil dihapus');
    }

    private function transform(SuratPeringatan $sp): array
    {
        $nama  = $sp->employee->nama ?? null;
        $noKtp = $sp->employee->no_ktp ?? null;

        return [
            'id' => $sp->id,
            'employee_id' => $sp->employee_id,

            // flatten
            'nama_karyawan' => $nama,
            'no_ktp' => $noKtp,

            // nested
            'employee' => $sp->employee ? [
                'id' => $sp->employee->id,
                'nama' => $nama,
                'no_ktp' => $noKtp,
            ] : null,

            'nomor_sp' => $sp->nomor_sp,
            'tanggal_sp' => optional($sp->tanggal_sp)->format('Y-m-d'),
            'tingkat' => $sp->tingkat,
            'pelanggaran' => $sp->pelanggaran,
            'tanggal_kejadian' => optional($sp->tanggal_kejadian)->format('Y-m-d'),

            // ✅ baru
            'periode_bulan' => $sp->periode_bulan,
            'tanggal_berakhir' => optional($sp->tanggal_berakhir)->format('Y-m-d'),

            'file_path' => $sp->file_path,
            'file_url' => $sp->file_path
                ? Storage::disk('public')->url($sp->file_path)
                : null,
        ];
    }
}
