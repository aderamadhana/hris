<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->get('per_page', 10));
        $perPage = max(1, min($perPage, 100)); // batas aman

        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status', 'all'); // aktif|nonaktif|all
        $publish = $request->get('publish', 'all'); // published|scheduled|expired|all

        $kategori = $request->get('kategori'); // filter kategori
        $diutamakan = $request->get('diutamakan'); // 1/0

        // whitelist sorting biar aman
        $allowedSort = [
            'tanggal_publish',
            'tanggal_berakhir',
            'judul',
            'kategori',
            'diutamakan',
            'urutan',
            'created_at',
        ];

        $sortBy  = $request->get('sort_by', 'tanggal_publish');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'tanggal_publish';
        }

        $q = Pengumuman::query()
            ->whereNull('deleted_at');

        // ✅ SEARCH
        if ($search !== '') {
            $q->where(function ($s) use ($search) {
                $s->where('judul', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        // ✅ STATUS (aktif/nonaktif)
        if ($status !== 'all' && $status !== null && $status !== '') {
            if ($status === 'aktif' || $status === 1 || $status === '1' || $status === true) {
                $q->where('aktif', 1);
            } elseif ($status === 'nonaktif' || $status === 0 || $status === '0' || $status === false) {
                $q->where('aktif', 0);
            }
        }

        // ✅ FILTER kategori
        if (!empty($kategori)) {
            $q->where('kategori', $kategori);
        }

        // ✅ FILTER diutamakan (pinned)
        if ($diutamakan !== null && $diutamakan !== '' && $diutamakan !== 'all') {
            if ($diutamakan === 1 || $diutamakan === '1' || $diutamakan === true) {
                $q->where('diutamakan', 1);
            } elseif ($diutamakan === 0 || $diutamakan === '0' || $diutamakan === false) {
                $q->where('diutamakan', 0);
            }
        }

        // ✅ FILTER publish status
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
        $paginator->getCollection()->transform(function ($p) {
            return [
                'id' => $p->id,
                'kategori' => $p->kategori,
                'judul' => $p->judul,
                'slug' => $p->slug,

                'ringkasan' => $p->ringkasan,
                'isi' => $p->isi,

                'diutamakan' => (bool) $p->diutamakan,
                'aktif' => (bool) $p->aktif,

                'tanggal_publish' => optional($p->tanggal_publish)?->format('Y-m-d H:i:s'),
                'tanggal_berakhir' => optional($p->tanggal_berakhir)?->format('Y-m-d H:i:s'),

                'urutan' => $p->urutan,
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
            'kategori' => ['required', 'string', 'max:50'],
            'judul' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:pengumumans,slug',
                'regex:/^[a-zA-Z0-9]+([-_][a-zA-Z0-9]+)*$/',
            ],

            'ringkasan' => ['nullable', 'string', 'max:255'],
            'isi' => ['nullable', 'string'],

            'diutamakan' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],

            'tanggal_publish' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'tanggal_berakhir' => ['nullable', 'date_format:Y-m-d\TH:i'],

            'urutan' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ], [
            'slug.regex' => 'Slug hanya boleh huruf/angka dan pemisah - atau _. Contoh: info-operasional / info_operasional',
        ]);

        // ✅ boolean (checkbox)
        $diutamakan = $request->has('diutamakan') ? $request->boolean('diutamakan') : false;
        $aktif = $request->has('aktif') ? $request->boolean('aktif') : true;

        // ✅ parse datetime-local
        $tanggalPublish = $request->filled('tanggal_publish')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_publish)
            : null;

        $tanggalBerakhir = $request->filled('tanggal_berakhir')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_berakhir)
            : null;

        Pengumuman::create([
            'kategori' => $validated['kategori'],
            'judul' => $validated['judul'],
            'slug' => $validated['slug'],

            'ringkasan' => $validated['ringkasan'] ?? null,
            'isi' => $validated['isi'] ?? null,

            'diutamakan' => $diutamakan,
            'aktif' => $aktif,

            'tanggal_publish' => $tanggalPublish,
            'tanggal_berakhir' => $tanggalBerakhir,

            'urutan' => $validated['urutan'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'kategori' => ['required', 'string', 'max:50'],
            'judul' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pengumumans', 'slug')->ignore($pengumuman->id),
                'regex:/^[a-zA-Z0-9]+([-_][a-zA-Z0-9]+)*$/',
            ],

            'ringkasan' => ['nullable', 'string', 'max:255'],
            'isi' => ['nullable', 'string'],

            'diutamakan' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],

            // 'tanggal_publish' => ['nullable', 'date_format:Y-m-d\TH:i'],
            // 'tanggal_berakhir' => ['nullable', 'date_format:Y-m-d\TH:i'],

            'urutan' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ], [
            'slug.regex' => 'Slug hanya boleh huruf/angka dan pemisah - atau _. Contoh: info-operasional / info_operasional',
        ]);

        // ✅ boolean (checkbox)
        $diutamakan = $request->has('diutamakan') ? $request->boolean('diutamakan') : $pengumuman->diutamakan;
        $aktif = $request->has('aktif') ? $request->boolean('aktif') : $pengumuman->aktif;

        // ✅ parse datetime-local
        $tanggalPublish = $request->filled('tanggal_publish')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_publish)
            : null;

        $tanggalBerakhir = $request->filled('tanggal_berakhir')
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal_berakhir)
            : null;

        $pengumuman->update([
            'kategori' => $validated['kategori'],
            'judul' => $validated['judul'],
            'slug' => $validated['slug'],

            'ringkasan' => $validated['ringkasan'] ?? null,
            'isi' => $validated['isi'] ?? null,

            'diutamakan' => $diutamakan,
            'aktif' => $aktif,

            'tanggal_publish' => $tanggalPublish,
            'tanggal_berakhir' => $tanggalBerakhir,

            'urutan' => $validated['urutan'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil diupdate');
    }

    public function delete($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        // Soft delete
        $pengumuman->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus');
    }
}
