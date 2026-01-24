<?php

namespace App\Http\Controllers;

use App\Models\{
    Loker,
    Perusahaan,
    Divisi
};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LokerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = max(1, min($perPage, 100)); // batas aman

        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status', 'all'); // aktif|nonaktif|all
        $publish = $request->get('publish', 'all'); // published|scheduled|expired|all

        $tipePekerjaan = $request->get('tipe_pekerjaan');
        $perusahaanId  = $request->get('perusahaan_id');
        $penempatanId  = $request->get('penempatan_id');
        $jamKerja      = $request->get('jam_kerja');

        // whitelist sorting biar aman
        $allowedSort = [
            'tanggal_publish',
            'tanggal_berakhir',
            'judul',
            'tipe_pekerjaan',
            'created_at',
        ];

        $sortBy = $request->get('sort_by', 'tanggal_publish');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'tanggal_publish';
        }

        $q = Loker::query()
            ->whereNull('deleted_at');

        // ✅ SEARCH
        if ($search !== '') {
            $q->where(function ($s) use ($search) {
                $s->where('judul', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('perusahaan_nama', 'like', "%{$search}%")
                ->orWhere('penempatan_nama', 'like', "%{$search}%")
                ->orWhere('tipe_pekerjaan', 'like', "%{$search}%")
                ->orWhere('jam_kerja', 'like', "%{$search}%");
            });
        }

        // ✅ STATUS (aktif/nonaktif)
        if ($status !== 'all' && $status !== null && $status !== '') {
            // terima juga "1"/"0"
            if ($status === 'aktif' || $status === 1 || $status === '1' || $status === true) {
                $q->where('aktif', 1);
            } elseif ($status === 'nonaktif' || $status === 0 || $status === '0' || $status === false) {
                $q->where('aktif', 0);
            }
        }

        // ✅ FILTER LAIN (opsional)
        if (!empty($tipePekerjaan)) {
            $q->where('tipe_pekerjaan', $tipePekerjaan);
        }

        if (!empty($perusahaanId)) {
            $q->where('perusahaan_id', (int) $perusahaanId);
        }

        if (!empty($penempatanId)) {
            $q->where('penempatan_id', (int) $penempatanId);
        }

        if (!empty($jamKerja)) {
            $q->where('jam_kerja', $jamKerja);
        }

        // ✅ FILTER publish status (opsional)
        if ($publish !== 'all' && $publish !== null && $publish !== '') {
            if ($publish === 'published') {
                $q->whereNotNull('tanggal_publish')
                ->where('tanggal_publish', '<=', now())
                ->where(function ($x) {
                    $x->whereNull('tanggal_berakhir')
                        ->orWhere('tanggal_berakhir', '>=', now());
                });
            }

            if ($publish === 'scheduled') {
                $q->whereNotNull('tanggal_publish')
                ->where('tanggal_publish', '>', now());
            }

            if ($publish === 'expired') {
                $q->whereNotNull('tanggal_berakhir')
                ->where('tanggal_berakhir', '<', now());
            }
        }

        // ✅ SORT
        $q->orderBy($sortBy, $sortDir);

        // ✅ PAGINATE
        $paginator = $q->paginate($perPage)->withQueryString();

        // ✅ Transform output biar rapih di tabel frontend
        $paginator->getCollection()->transform(function ($l) {
            return [
                'id' => $l->id,
                'judul' => $l->judul,
                'slug' => $l->slug,
                'tipe_pekerjaan' => $l->tipe_pekerjaan,
                'perusahaan_nama' => $l->perusahaan_nama,
                'penempatan_nama' => $l->penempatan_nama,
                'jam_kerja' => $l->jam_kerja,
                'gaji_min' => $l->gaji_min,
                'gaji_max' => $l->gaji_max,
                'mata_uang' => $l->mata_uang,
                'aktif' => (bool) $l->aktif,
                'tanggal_publish' => optional($l->tanggal_publish)?->format('Y-m-d H:i:s'),
                'tanggal_berakhir' => optional($l->tanggal_berakhir)?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'links' => [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:lokers,slug',
                'regex:/^[a-zA-Z0-9]+([-_][a-zA-Z0-9]+)*$/',
            ],

            'tipe_pekerjaan' => ['required', 'string', 'max:50'],

            // ✅ hanya ID, nama diambil dari master
            'perusahaan_id' => ['required', 'integer', 'exists:perusahaan,id'],
            'penempatan_id' => ['required', 'integer', 'exists:divisi,id'],

            'jam_kerja' => ['nullable', 'string', 'max:50'],

            'ringkasan' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'persyaratan' => ['nullable', 'string'],

            'gaji_min' => ['nullable', 'integer', 'min:0'],
            'gaji_max' => ['nullable', 'integer', 'min:0'],
            'mata_uang' => ['nullable', 'string', 'max:3'],

            'link_lamar' => ['nullable', 'string', 'max:255'],
            'whatsapp_kontak' => ['nullable', 'string', 'max:30'],

            'aktif' => ['nullable', 'boolean'],

            'tanggal_publish' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'tanggal_berakhir' => ['nullable', 'date_format:Y-m-d\TH:i'],
        ], [
            'slug.regex' => 'Slug hanya boleh huruf/angka dan pemisah - atau _. Contoh: security-guard / security_guard',
        ]);

        // ✅ ambil master perusahaan
        $perusahaan = Perusahaan::findOrFail((int) $validated['perusahaan_id']);

        // ✅ ambil master divisi (penempatan)
        $divisi = Divisi::findOrFail((int) $validated['penempatan_id']);

        // ✅ boolean
        $validated['aktif'] = $request->boolean('aktif');

        // ✅ parse datetime-local
        $tanggalPublish = $request->filled('tanggal_publish')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_publish)
            : null;

        $tanggalBerakhir = $request->filled('tanggal_berakhir')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_berakhir)
            : null;

        // ✅ validasi tambahan gaji
        if (!is_null($validated['gaji_min']) && !is_null($validated['gaji_max'])) {
            if ((int) $validated['gaji_max'] < (int) $validated['gaji_min']) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => [
                        'gaji_max' => ['Gaji maksimum tidak boleh lebih kecil dari gaji minimum.'],
                    ],
                ], 422);
            }
        }

        $loker = Loker::create([
            'judul' => $validated['judul'],
            'slug' => $validated['slug'],
            'tipe_pekerjaan' => $validated['tipe_pekerjaan'],

            // ✅ simpan ID + nama hasil master
            'perusahaan_id' => (int) $validated['perusahaan_id'],
            'perusahaan_nama' => $perusahaan->nama_perusahaan ?? $perusahaan->nama ?? null,

            'penempatan_id' => (int) $validated['penempatan_id'],
            'penempatan_nama' => $divisi->nama_divisi ?? $divisi->nama ?? null,

            'jam_kerja' => $validated['jam_kerja'] ?? null,

            'ringkasan' => $validated['ringkasan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'persyaratan' => $validated['persyaratan'] ?? null,

            'gaji_min' => $validated['gaji_min'] ?? null,
            'gaji_max' => $validated['gaji_max'] ?? null,
            'mata_uang' => $validated['mata_uang'] ?? 'IDR',

            'link_lamar' => $validated['link_lamar'] ?? null,
            'whatsapp_kontak' => $validated['whatsapp_kontak'] ?? null,

            'aktif' => $validated['aktif'],

            'tanggal_publish' => $tanggalPublish,
            'tanggal_berakhir' => $tanggalBerakhir,
        ]);

        return redirect()->back()->with('success', 'Loker berhasil disimpan');
    }
}
