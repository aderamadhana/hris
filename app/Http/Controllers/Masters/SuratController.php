<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $page = (int) $request->get('page', 1);

        $query = Surat::query();

        // search: nomor_surat / perihal
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // filter tanggal (opsional)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_surat', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_surat', '<=', $request->tanggal_sampai);
        }

        $query->orderByDesc('tanggal_surat')->orderByDesc('id');

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $items->getCollection()->map(function ($s) {
            return [
                'id' => $s->id,
                'nomor_surat' => $s->nomor_surat,
                'tanggal_surat' => optional($s->tanggal_surat)->format('Y-m-d'),
                'perihal' => $s->perihal,
                'file_path' => $s->file_path,
                'file_url' => $s->file_path ? Storage::disk('public')->url($s->file_path) : null,
            ];
        });

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
                'tanggal_dari' => $request->tanggal_dari ?? '',
                'tanggal_sampai' => $request->tanggal_sampai ?? '',
            ],
        ]);
    }

    public function getData(int $id)
    {
        $s = Surat::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $s->id,
                'nomor_surat' => $s->nomor_surat,
                'tanggal_surat' => optional($s->tanggal_surat)->format('Y-m-d'),
                'perihal' => $s->perihal,
                'file_path' => $s->file_path,
                'file_url' => $s->file_path ? Storage::disk('public')->url($s->file_path) : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:50',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'file' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $path = $request->file('file')->store('surat', 'public');

        Surat::create([
            'nomor_surat' => $validated['nomor_surat'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'perihal' => $validated['perihal'],
            'file_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Surat berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $s = Surat::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:50',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'file' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($request->hasFile('file')) {
            if ($s->file_path && Storage::disk('public')->exists($s->file_path)) {
                Storage::disk('public')->delete($s->file_path);
            }
            $s->file_path = $request->file('file')->store('surat', 'public');
        }

        $s->nomor_surat = $validated['nomor_surat'];
        $s->tanggal_surat = $validated['tanggal_surat'];
        $s->perihal = $validated['perihal'];
        $s->save();

        return redirect()->back()->with('success', 'Surat berhasil diupdate');
    }

    public function destroy($id)
    {
        $s = Surat::findOrFail($id);

        if ($s->file_path && Storage::disk('public')->exists($s->file_path)) {
            Storage::disk('public')->delete($s->file_path);
        }

        $s->delete();

        return redirect()->back()->with('success', 'Surat berhasil dihapus');
    }
}
