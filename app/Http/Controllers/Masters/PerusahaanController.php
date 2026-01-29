<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\Employee;
use App\Models\Divisi;
use App\Models\EmployeeEmployment;
use App\Models\HistoryMou;
use App\Exports\PerusahaanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);

        // FIX: Request::string()->trim() menghasilkan Stringable, jadi harus dicast ke string
        $search  = (string) $request->string('search')->trim();
        $status  = (string) $request->string('status')->trim();

        $query = Perusahaan::query();

        if ($search !== '') {
            $query->where('nama_perusahaan', 'like', "%{$search}%");
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $result = $query->paginate($perPage);

        $data = $result->getCollection()->map(function ($perusahaan) {

            // Hitung karyawan aktif yang employment terbarunya di perusahaan ini
            $totalKaryawanAktif = DB::table('employees as e')
                ->join('employee_employment_histories as eeh', function($join) {
                    $join->on('e.id', '=', 'eeh.employee_id')
                        ->whereRaw('eeh.id = (
                            SELECT MAX(id)
                            FROM employee_employment_histories
                            WHERE employee_id = e.id
                        )');
                })
                ->where('e.status_active', '1')
                ->where(function($q) use ($perusahaan) {
                    $q->where('eeh.perusahaan', $perusahaan->nama_perusahaan)
                      ->orWhere('eeh.perusahaan', 'like', '%' . $perusahaan->nama_perusahaan . '%');
                })
                ->count();

            // Total history MoU dari tabel history_mous
            $totalHistoryMou = DB::table('history_mous')
                ->where('perusahaan_id', $perusahaan->id)
                ->count();

            return [
                'id' => $perusahaan->id,
                'kode_perusahaan' => $perusahaan->kode_perusahaan ?? '-',
                'nama_perusahaan' => $perusahaan->nama_perusahaan,
                'alamat' => $perusahaan->alamat ?? '-',
                'tanggal_awal_mou' => $perusahaan->tanggal_awal_mou,
                'tanggal_akhir_mou' => $perusahaan->tanggal_akhir_mou,
                'berkas_mou' => $perusahaan->berkas_mou,
                'berkas_mou_url' => $perusahaan->berkas_mou ? asset('storage/' . $perusahaan->berkas_mou) : null,
                'keterangan' => $perusahaan->keterangan,
                'status' => $perusahaan->status ?? 'aktif',
                'total_karyawan_aktif' => $totalKaryawanAktif,

                // pertahankan key lama biar frontend tidak pecah
                'total_history' => $totalHistoryMou,
                'total_history_mou' => $totalHistoryMou,

                'created_at' => $perusahaan->created_at,
                'updated_at' => $perusahaan->updated_at,
            ];
        });

        $aktif = Perusahaan::query()->where('status', 'aktif');
        $tidak_aktif = Perusahaan::query()->where('status', 'tidak_aktif');

        $totalAllActive = (clone $aktif)->count();
        $totalAllNonActive = (clone $tidak_aktif)->count();

        return response()->json([
            'data' => $data,
            'statistics' => [
                'total_all_active' => $totalAllActive,
                'total_all_non_active' => $totalAllNonActive,
            ],
            'meta' => [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/marketing/client-aktif/add-client');
    }

    public function getData(int $id)
    {
        $perusahaan = Perusahaan::query()
            ->with([
                'divisi' => function ($q) {
                    $q->select(
                        'id',
                        'perusahaan_id',
                        'nama_divisi',
                        'kode_divisi',
                        'alamat_penempatan',
                        'latitude',
                        'longitude',
                        'radius_presensi',
                        'keterangan',
                        'status',
                        'tanggal_awal_mou',
                        'tanggal_akhir_mou',
                        'created_at',
                        'updated_at'
                    )
                    ->orderBy('nama_divisi');
                },
                'historyMou' => function ($q) {
                    $q->select(
                        'id',
                        'perusahaan_id',
                        'tanggal_awal_mou',
                        'tanggal_akhir_mou',
                        'berkas_mou',
                        'keterangan',
                        'status',
                        'created_at'
                    )
                    ->latest('tanggal_awal_mou');
                }
            ])
            ->select(
                'id',
                'kode_perusahaan',
                'nama_perusahaan',
                'alamat',
                'tanggal_awal_mou',
                'tanggal_akhir_mou',
                'berkas_mou',
                'keterangan',
                'status',
                'created_at',
                'updated_at'
            )
            ->findOrFail($id);

        // Tambahkan URL file untuk history (biar frontend gampang)
        $perusahaan->historyMou->transform(function ($m) {
            $m->berkas_mou_url = $m->berkas_mou ? asset('storage/' . $m->berkas_mou) : null;
            return $m;
        });

        $perusahaan->berkas_mou_url = $perusahaan->berkas_mou ? asset('storage/' . $perusahaan->berkas_mou) : null;

        return response()->json([
            'data' => $perusahaan,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_perusahaan' => ['required','string','max:20', Rule::unique('perusahaan', 'kode_perusahaan')],
            'nama_perusahaan' => ['required','string','max:200'],
            'alamat' => ['required','string'],
            'status' => ['required', Rule::in(['aktif','tidak_aktif'])],

            'tanggal_awal_mou' => ['nullable','date'],
            'tanggal_akhir_mou' => ['nullable','date','after_or_equal:tanggal_awal_mou'],
            'keterangan' => ['nullable','string'],

            'berkas_mou' => ['nullable','file','mimes:pdf,doc,docx','max:5120'],

            'divisi' => ['array'],
            'divisi.*.nama_divisi' => ['required','string','max:100'],
            'divisi.*.status' => ['required', Rule::in(['aktif','tidak_aktif'])],
            'divisi.*.alamat_penempatan' => ['nullable','string'],
            'divisi.*.radius_presensi' => ['nullable','integer','min:0'],
            'divisi.*.latitude' => ['nullable','numeric'],
            'divisi.*.longitude' => ['nullable','numeric'],
            'divisi.*.tanggal_awal_mou' => ['nullable','date'],
            'divisi.*.tanggal_akhir_mou' => ['nullable','date','after_or_equal:divisi.*.tanggal_awal_mou'],
        ]);

        DB::beginTransaction();

        try {
            $perusahaan = Perusahaan::create([
                'kode_perusahaan' => $validated['kode_perusahaan'],
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'alamat' => $validated['alamat'],
                'status' => $validated['status'],
                'tanggal_awal_mou' => $validated['tanggal_awal_mou'] ?? null,
                'tanggal_akhir_mou' => $validated['tanggal_akhir_mou'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Upload MoU
            $path = null;
            if ($request->hasFile('berkas_mou')) {
                $path = $request->file('berkas_mou')->store("mou/perusahaan/{$perusahaan->id}", 'public');
                $perusahaan->update(['berkas_mou' => $path]);
            }

            // Buat history MoU (kalau ada payload MoU)
            $hasMouPayload = ($validated['tanggal_awal_mou'] ?? null) || ($validated['tanggal_akhir_mou'] ?? null) || $path;
            if ($hasMouPayload) {
                HistoryMou::create([
                    'perusahaan_id' => $perusahaan->id,
                    'tanggal_awal_mou' => $validated['tanggal_awal_mou'] ?? null,
                    'tanggal_akhir_mou' => $validated['tanggal_akhir_mou'] ?? null,
                    'berkas_mou' => $path,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => 'aktif',
                ]);
            }

            // Simpan divisi
            foreach (($validated['divisi'] ?? []) as $d) {
                $perusahaan->divisi()->create([
                    'nama_divisi' => $d['nama_divisi'],
                    'status' => $d['status'],
                    'alamat_penempatan' => $d['alamat_penempatan'] ?? null,
                    'radius_presensi' => $d['radius_presensi'] ?? 500,
                    'latitude' => $d['latitude'] ?? null,
                    'longitude' => $d['longitude'] ?? null,
                    'tanggal_awal_mou' => $d['tanggal_awal_mou'] ?? null,
                    'tanggal_akhir_mou' => $d['tanggal_akhir_mou'] ?? null,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Perusahaan berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['server' => 'Terjadi kesalahan saat menyimpan data.'])
                ->withInput();
        }
    }

    public function edit(Perusahaan $id)
    {
        return Inertia::render('admin/marketing/client-aktif/edit-client', [
            'perusahaan' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);

        $validated = $request->validate([
            'kode_perusahaan' => [
                'required','string','max:20',
                Rule::unique('perusahaan', 'kode_perusahaan')->ignore($perusahaan->id),
            ],
            'nama_perusahaan' => ['required','string','max:200'],
            'alamat' => ['required','string'],
            'status' => ['required', Rule::in(['aktif','tidak_aktif'])],

            'tanggal_awal_mou' => ['nullable','date'],
            'tanggal_akhir_mou' => ['nullable','date','after_or_equal:tanggal_awal_mou'],
            'keterangan' => ['nullable','string'],
            'berkas_mou' => ['nullable','file','mimes:pdf,doc,docx','max:5120'],

            'divisi' => ['array'],
            'divisi.*.id' => ['nullable','integer'],
            'divisi.*.nama_divisi' => ['required','string','max:100'],
            'divisi.*.status' => ['required', Rule::in(['aktif','tidak_aktif'])],
            'divisi.*.alamat_penempatan' => ['nullable','string'],
            'divisi.*.radius_presensi' => ['nullable','integer','min:0'],
            'divisi.*.latitude' => ['nullable','numeric'],
            'divisi.*.longitude' => ['nullable','numeric'],
            'divisi.*.tanggal_awal_mou' => ['nullable','date'],
            'divisi.*.tanggal_akhir_mou' => ['nullable','date','after_or_equal:divisi.*.tanggal_awal_mou'],
        ]);

        DB::beginTransaction();

        try {
            // Update perusahaan (basic)
            $perusahaan->update([
                'kode_perusahaan' => $validated['kode_perusahaan'],
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'alamat' => $validated['alamat'],
                'status' => $validated['status'],
                'tanggal_awal_mou' => $validated['tanggal_awal_mou'] ?? null,
                'tanggal_akhir_mou' => $validated['tanggal_akhir_mou'] ?? null,
                'keterangan' => $validated['keterangan'] ?? $perusahaan->keterangan,
            ]);

            // Upload file baru (kalau ada)
            $uploadedPath = null;
            if ($request->hasFile('berkas_mou')) {
                $uploadedPath = $request->file('berkas_mou')->store("mou/perusahaan/{$perusahaan->id}", 'public');
                $perusahaan->update(['berkas_mou' => $uploadedPath]);
            }

            // ====== HISTORY MOU: buat record baru kalau ada perubahan ======
            $newAwal = $validated['tanggal_awal_mou'] ?? null;
            $newAkhir = $validated['tanggal_akhir_mou'] ?? null;
            $newKet = $validated['keterangan'] ?? $perusahaan->keterangan;
            $newFile = $uploadedPath ?? $perusahaan->berkas_mou;

            $hasMouPayload = $newAwal || $newAkhir || $uploadedPath;
            if ($hasMouPayload) {
                $last = $perusahaan->historyMou()->latest('tanggal_awal_mou')->first();

                $lastAwal = $last && $last->tanggal_awal_mou ? Carbon::parse($last->tanggal_awal_mou)->toDateString() : null;
                $lastAkhir = $last && $last->tanggal_akhir_mou ? Carbon::parse($last->tanggal_akhir_mou)->toDateString() : null;

                $newAwalStr = $newAwal ? Carbon::parse($newAwal)->toDateString() : null;
                $newAkhirStr = $newAkhir ? Carbon::parse($newAkhir)->toDateString() : null;

                $changed = !$last
                    || ($lastAwal !== $newAwalStr)
                    || ($lastAkhir !== $newAkhirStr)
                    || ((string) ($last->keterangan ?? '') !== (string) ($newKet ?? ''))
                    || ((string) ($last->berkas_mou ?? '') !== (string) ($newFile ?? ''));

                if ($changed) {
                    HistoryMou::create([
                        'perusahaan_id' => $perusahaan->id,
                        'tanggal_awal_mou' => $newAwal,
                        'tanggal_akhir_mou' => $newAkhir,
                        'keterangan' => $newKet,
                        'berkas_mou' => $newFile,
                        'status' => 'aktif',
                    ]);
                }
            }

            // ====== DIVISI ======
            $divisiPayload = $validated['divisi'] ?? [];
            $keptIds = [];

            foreach ($divisiPayload as $d) {
                $data = [
                    'nama_divisi' => $d['nama_divisi'],
                    'status' => $d['status'],
                    'alamat_penempatan' => $d['alamat_penempatan'] ?? null,
                    'radius_presensi' => $d['radius_presensi'] ?? 500,
                    'latitude' => $d['latitude'] ?? null,
                    'longitude' => $d['longitude'] ?? null,
                    'tanggal_awal_mou' => $d['tanggal_awal_mou'] ?? null,
                    'tanggal_akhir_mou' => $d['tanggal_akhir_mou'] ?? null,
                ];

                $divisi = $perusahaan->divisi()->updateOrCreate(
                    ['id' => $d['id'] ?? null],
                    $data
                );

                $keptIds[] = $divisi->id;
            }

            $perusahaan->divisi()
                ->when(count($keptIds) > 0, fn($q) => $q->whereNotIn('id', $keptIds))
                ->when(count($keptIds) === 0, fn($q) => $q)
                ->delete();

            DB::commit();

            return back()->with('success', 'Data perusahaan berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['server' => 'Terjadi kesalahan saat menyimpan data.'])
                ->withInput();
        }
    }

    // FIX: Hindari typehint class yang tidak jelas (PayrollPeriod) karena bisa bikin fatal error saat file di-load
    public function destroy($id)
    {
        //
    }

    public function sync(Request $request)
    {
        $rows = EmployeeEmployment::query()
            ->select('perusahaan', 'penempatan')
            ->whereNotNull('perusahaan')
            ->where('perusahaan', '!=', '')
            ->distinct()
            ->get();

        $stats = [
            'perusahaan_created' => 0,
            'perusahaan_skipped' => 0,
            'divisi_created' => 0,
            'divisi_skipped' => 0,
            'source_rows' => $rows->count(),
        ];

        DB::transaction(function () use ($rows, &$stats) {

            $existingPerusahaan = Perusahaan::withTrashed()
                ->get(['id', 'nama_perusahaan', 'deleted_at', 'kode_perusahaan'])
                ->mapWithKeys(function ($p) {
                    return [$this->normalizeKey($p->nama_perusahaan) => $p];
                });

            $usedKode = Perusahaan::withTrashed()
                ->pluck('kode_perusahaan')
                ->filter()
                ->map(fn ($v) => Str::upper(trim($v)))
                ->flip();

            $existingDivisiCache = []; // [perusahaan_id => [normalizedNamaDivisi => Divisi]]

            foreach ($rows as $r) {
                $namaPerusahaan = $this->cleanText((string) $r->perusahaan);
                if ($namaPerusahaan === '') {
                    continue;
                }

                $pKey = $this->normalizeKey($namaPerusahaan);

                // ====== CREATE PERUSAHAAN ONLY (NO UPDATE/RESTORE) ======
                if (isset($existingPerusahaan[$pKey])) {
                    $perusahaan = $existingPerusahaan[$pKey];
                    $stats['perusahaan_skipped']++;

                    if ($perusahaan->trashed()) {
                        continue;
                    }
                } else {
                    $kode = $this->generateUniqueKodePerusahaan($namaPerusahaan, $usedKode);

                    $perusahaan = Perusahaan::create([
                        'kode_perusahaan'  => $kode,
                        'nama_perusahaan'  => $namaPerusahaan,
                        'alamat'           => '-',
                        'keterangan'       => 'Auto-sync dari employee_employment_histories',
                        'status'           => 'aktif',
                    ]);

                    $existingPerusahaan[$pKey] = $perusahaan;
                    $stats['perusahaan_created']++;

                    $usedKode[Str::upper($kode)] = true;
                }

                // ====== CREATE DIVISI ONLY (NO UPDATE/RESTORE) ======
                $namaDivisi = $this->cleanText((string) $r->penempatan);
                if ($namaDivisi === '') {
                    continue;
                }

                $perusahaanId = $perusahaan->id;
                $dKey = $this->normalizeKey($namaDivisi);

                if (!isset($existingDivisiCache[$perusahaanId])) {
                    $existingDivisiCache[$perusahaanId] = Divisi::withTrashed()
                        ->where('perusahaan_id', $perusahaanId)
                        ->get(['id', 'perusahaan_id', 'nama_divisi', 'deleted_at'])
                        ->mapWithKeys(function ($d) {
                            return [$this->normalizeKey($d->nama_divisi) => $d];
                        })
                        ->all();
                }

                if (isset($existingDivisiCache[$perusahaanId][$dKey])) {
                    $stats['divisi_skipped']++;
                    continue;
                }

                $divisi = Divisi::create([
                    'perusahaan_id' => $perusahaanId,
                    'nama_divisi'   => $namaDivisi,
                    'keterangan'    => 'Auto-sync dari employee_employment_histories (penempatan)',
                    'status'        => 'aktif',
                ]);

                $existingDivisiCache[$perusahaanId][$dKey] = $divisi;
                $stats['divisi_created']++;
            }
        });

        return response()->json([
            'message' => 'Sync perusahaan & divisi selesai (create-only)',
            'stats' => $stats,
        ]);
    }

    private function cleanText(string $text): string
    {
        return (string) Str::of($text)->squish();
    }

    private function normalizeKey(string $text): string
    {
        return (string) Str::of($text)->squish()->lower();
    }

    private function generateUniqueKodePerusahaan(string $namaPerusahaan, $usedKodeSet): string
    {
        $clean = Str::of($namaPerusahaan)
            ->upper()
            ->replaceMatches('/[^A-Z0-9 ]+/', ' ')
            ->squish();

        $words = array_values(array_filter(explode(' ', (string) $clean)));

        if (count($words) === 0) {
            $base = 'COMP';
        } elseif (count($words) === 1) {
            $base = substr($words[0], 0, 5);
        } else {
            $initials = '';
            foreach ($words as $w) {
                $initials .= substr($w, 0, 1);
                if (strlen($initials) >= 10) break;
            }
            $base = $initials ?: substr($words[0], 0, 5);
        }

        $base = substr($base, 0, 18);
        $candidate = $base;

        $i = 1;
        while (isset($usedKodeSet[Str::upper($candidate)])) {
            $suffix = (string) $i;
            $candidate = substr($base, 0, 20 - strlen($suffix)) . $suffix;
            $i++;
        }

        return $candidate;
    }

    public function downloadPerusahaan(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        return Excel::download(
            new PerusahaanExport($search, $status),
            'perusahaan.xlsx'
        );
    }
}
