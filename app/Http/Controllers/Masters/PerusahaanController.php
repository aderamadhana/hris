<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\EmployeeEmployment;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                        'created_at',
                        'updated_at'
                    )
                    ->orderBy('nama_divisi');
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

        return response()->json([
            'data' => $perusahaan,
        ]);
    }


    public function store(Request $request)
    {
        
    }
        

    public function edit(Perusahaan $id)
    {
        return Inertia::render('master/client/edit-client', [
            'perusahaan' => $id
        ]);
    }

    public function update(Request $request, Perusahaan $payrollPeriod)
    {
        
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        
    }

    public function sync(Request $request)
    {
        // Ambil kombinasi unik perusahaan + penempatan dari riwayat kerja
        $rows = EmployeeEmployment::query()
            ->select('perusahaan', 'penempatan')
            ->whereNotNull('perusahaan')
            ->where('perusahaan', '!=', '')
            ->distinct()
            ->get();

        $stats = [
            'perusahaan_created' => 0,
            'perusahaan_restored' => 0,
            'perusahaan_skipped' => 0,
            'divisi_created' => 0,
            'divisi_restored' => 0,
            'divisi_skipped' => 0,
            'source_rows' => $rows->count(),
        ];

        DB::transaction(function () use ($rows, &$stats) {
            // Cache existing perusahaan by normalized name (include soft deleted)
            $existingPerusahaan = Perusahaan::withTrashed()
                ->get(['id', 'nama_perusahaan', 'deleted_at', 'kode_perusahaan'])
                ->mapWithKeys(function ($p) {
                    return [$this->normalizeKey($p->nama_perusahaan) => $p];
                });

            // Cache used kode_perusahaan (include soft deleted)
            $usedKode = Perusahaan::withTrashed()
                ->pluck('kode_perusahaan')
                ->filter()
                ->map(fn ($v) => Str::upper(trim($v)))
                ->flip(); // set-like

            // Cache existing divisi by (perusahaan_id + normalized divisi)
            // (dibangun incremental saat perusahaan ditemukan)
            $existingDivisiCache = []; // [perusahaan_id => [key => DivisiModel]]

            foreach ($rows as $r) {
                $namaPerusahaanRaw = (string) $r->perusahaan;
                $namaPerusahaan = $this->cleanText($namaPerusahaanRaw);

                if ($namaPerusahaan === '') {
                    continue;
                }

                $pKey = $this->normalizeKey($namaPerusahaan);

                // ====== UPSERT PERUSAHAAN (by normalized name) ======
                if (isset($existingPerusahaan[$pKey])) {
                    $perusahaan = $existingPerusahaan[$pKey];

                    if ($perusahaan->trashed()) {
                        $perusahaan->restore();
                        $perusahaan->status = 'aktif';
                        $perusahaan->save();
                        $stats['perusahaan_restored']++;
                    } else {
                        $stats['perusahaan_skipped']++;
                    }
                } else {
                    $kode = $this->generateUniqueKodePerusahaan($namaPerusahaan, $usedKode);

                    $perusahaan = Perusahaan::create([
                        'kode_perusahaan' => $kode,
                        'nama_perusahaan' => $namaPerusahaan,
                        // kolom alamat NOT NULL -> isi default aman
                        'alamat' => '-',
                        'keterangan' => 'Auto-sync dari employee_employment_histories',
                        'status' => 'aktif',
                    ]);

                    $existingPerusahaan[$pKey] = $perusahaan;
                    $stats['perusahaan_created']++;

                    $usedKode[Str::upper($kode)] = true;
                }

                // ====== UPSERT DIVISI (penempatan) ======
                $penempatanRaw = (string) $r->penempatan;
                $namaDivisi = $this->cleanText($penempatanRaw);

                // kalau penempatan kosong, skip
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
                    $divisi = $existingDivisiCache[$perusahaanId][$dKey];

                    if ($divisi->trashed()) {
                        $divisi->restore();
                        $divisi->status = 'aktif';
                        $divisi->save();
                        $stats['divisi_restored']++;
                    } else {
                        $stats['divisi_skipped']++;
                    }
                } else {
                    $divisi = Divisi::create([
                        'perusahaan_id' => $perusahaanId,
                        'nama_divisi' => $namaDivisi,
                        'keterangan' => 'Auto-sync dari employee_employment_histories (penempatan)',
                        'status' => 'aktif',
                        // radius_presensi sudah default di migration (500), boleh override kalau mau
                    ]);

                    $existingDivisiCache[$perusahaanId][$dKey] = $divisi;
                    $stats['divisi_created']++;
                }
            }
        });

        return response()->json([
            'message' => 'Sync perusahaan & divisi selesai',
            'stats' => $stats,
        ]);
    }

    private function cleanText(string $text): string
    {
        // trim + collapse whitespace
        return (string) Str::of($text)->squish();
    }

    private function normalizeKey(string $text): string
    {
        // key untuk dedup: lower + squish (cukup konservatif, tidak “menggabungkan” terlalu agresif)
        return (string) Str::of($text)->squish()->lower();
    }

    private function generateUniqueKodePerusahaan(string $namaPerusahaan, $usedKodeSet): string
    {
        // base code: ambil inisial kata (maks 10 char), fallback 3 huruf awal kalau 1 kata
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

        $base = substr($base, 0, 18); // sisakan ruang buat suffix angka (max 20)
        $candidate = $base;

        $i = 1;
        while (isset($usedKodeSet[Str::upper($candidate)])) {
            $suffix = (string) $i;
            $candidate = substr($base, 0, 20 - strlen($suffix)) . $suffix;
            $i++;
        }

        return $candidate;
    }

}