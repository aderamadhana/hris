<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\RekapPresensiHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'perusahaan_id' => 'required',
            'divisi_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|max:5120', // 5MB
            'jenis_presensi' => 'required|in:masuk,pulang',
            'tanggal' => 'required|date',
            'jarak_dari_lokasi' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $employee = Employee::with('shift')->findOrFail($request->employee_id);

            if (!$employee->shift_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan belum memiliki shift. Hubungi admin.',
                ], 422);
            }

            // Upload foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'presensi_' . $request->employee_id . '_' . time() . '.' . $file->extension();
                $fotoPath = $file->storeAs('presensi', $filename, 'public');
            }

            // Simpan presensi
            $presensi = Presensi::create([
                'employee_id' => $request->employee_id,
                'shift_id' => $employee->shift_id,
                'perusahaan_id' => $request->perusahaan_id,
                'divisi_id' => $request->divisi_id,
                'tanggal_presensi' => $request->tanggal,
                'jenis_presensi' => $request->jenis_presensi,
                'waktu_presensi' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'akurasi_gps' => $request->accuracy,
                'jarak_dari_lokasi' => $request->jarak_dari_lokasi,
                'foto_presensi' => $fotoPath,
                'status' => $this->determineStatus($request, $employee->shift),
            ]);

            // Update atau buat rekap harian
            $this->updateRekapHarian($presensi);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil disimpan',
                'data' => $presensi,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus foto jika ada error
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }

            // Log error
            Log::error('Presensi Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'request' => $request->except(['foto']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan presensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function determineStatus($request, $shift)
    {
        // Validasi jarak
        if ($request->has('in_range') && !$request->boolean('in_range')) {
            return 'tidak_valid';
        }

        // Validasi akurasi GPS
        if ($request->has('accuracy') && $request->accuracy > 100) {
            return 'perlu_verifikasi';
        }

        // Cek keterlambatan (jika masuk)
        if ($request->jenis_presensi === 'masuk' && $shift) {
            $waktuPresensi = now();
            $jamMasukShift = Carbon::parse($shift->jam_masuk);
            $toleransi = $shift->toleransi_keterlambatan ?? 15; // default 15 menit

            // Tambahkan toleransi
            $batasMasuk = $jamMasukShift->copy()->addMinutes($toleransi);

            if ($waktuPresensi->gt($batasMasuk)) {
                return 'terlambat';
            }
        }

        return 'hadir';
    }

    private function updateRekapHarian($presensi)
    {
        // Cari atau buat rekap baru
        $rekap = RekapPresensiHarian::firstOrNew([
            'employee_id' => $presensi->employee_id,
            'tanggal' => $presensi->tanggal_presensi,
            'perusahaan_id' => $presensi->perusahaan_id,
            'divisi_id' => $presensi->divisi_id
        ]);

        // PENTING: Set employee_id dan tanggal jika record baru
        if (!$rekap->exists) {
            $rekap->employee_id = $presensi->employee_id;
            $rekap->tanggal = $presensi->tanggal_presensi;
            $rekap->shift_id = $presensi->shift_id;
        }

        // Update data berdasarkan jenis presensi
        if ($presensi->jenis_presensi === 'masuk') {
            $rekap->waktu_masuk = $presensi->waktu_presensi;
            $rekap->foto_masuk = $presensi->foto_presensi;
        } else {
            $rekap->waktu_pulang = $presensi->waktu_presensi;
            $rekap->foto_pulang = $presensi->foto_presensi;
        }

        // Update status kehadiran
        $rekap->status_kehadiran = $presensi->status;

        // Hitung total jam kerja jika sudah ada masuk dan pulang
        if ($rekap->waktu_masuk && $rekap->waktu_pulang) {
            $masuk = \Carbon\Carbon::parse($rekap->waktu_masuk);
            $pulang = \Carbon\Carbon::parse($rekap->waktu_pulang);
            $rekap->total_jam_kerja = $pulang->diffInMinutes($masuk);
        }

        $rekap->save();

        return $rekap;
    }

    public function logHarian(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'nullable|date',
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000|max:2100',
        ]);

        $employeeId = $request->employee_id;
        
        // Default ke hari ini jika tidak ada parameter
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        $bulan = $request->bulan ?? $tanggal->month;
        $tahun = $request->tahun ?? $tanggal->year;

        try {
            // Query untuk satu hari spesifik
            if ($request->tanggal) {
                $log = $this->getLogHariIni($employeeId, $tanggal);
                
                return response()->json([
                    'success' => true,
                    'data' => $log,
                ]);
            }

            // Query untuk satu bulan
            $logs = $this->getLogBulanan($employeeId, $bulan, $tahun);
            $summary = $this->getSummaryBulanan($employeeId, $bulan, $tahun);

            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => $logs,
                    'summary' => $summary,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil log presensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get log presensi untuk hari tertentu
     */
    private function getLogHariIni($employeeId, $tanggal)
    {
        // Ambil rekap harian
        $rekap = RekapPresensiHarian::where('employee_id', $employeeId)
            ->where('tanggal', $tanggal->format('Y-m-d'))
            ->first();

        // Ambil detail presensi (masuk & pulang)
        $presensiDetail = Presensi::where('employee_id', $employeeId)
            ->where('tanggal_presensi', $tanggal->format('Y-m-d'))
            ->orderBy('waktu_presensi', 'asc')
            ->get();

        return [
            'tanggal' => $tanggal->format('Y-m-d'),
            'tanggal_formatted' => $tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'rekap' => $rekap,
            'detail' => $presensiDetail->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis_presensi' => $item->jenis_presensi,
                    'waktu_presensi' => $item->waktu_presensi,
                    'waktu_formatted' => Carbon::parse($item->waktu_presensi)->format('H:i'),
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'akurasi_gps' => $item->akurasi_gps,
                    'jarak_dari_lokasi' => $item->jarak_dari_lokasi,
                    'foto_presensi' => $item->foto_presensi ? asset('storage/' . $item->foto_presensi) : null,
                    'status' => $item->status,
                ];
            }),
            'status' => [
                'sudah_masuk' => $presensiDetail->where('jenis_presensi', 'masuk')->isNotEmpty(),
                'sudah_pulang' => $presensiDetail->where('jenis_presensi', 'pulang')->isNotEmpty(),
            ],
        ];
    }

    /**
     * Get log presensi untuk satu bulan
     */
    private function getLogBulanan($employeeId, $bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $logs = RekapPresensiHarian::where('employee_id', $employeeId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'tanggal_formatted' => Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                    'hari' => Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd'),
                    'waktu_masuk' => $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->format('H:i') : null,
                    'waktu_pulang' => $item->waktu_pulang ? Carbon::parse($item->waktu_pulang)->format('H:i') : null,
                    'foto_masuk' => $item->foto_masuk ? asset('storage/' . $item->foto_masuk) : null,
                    'foto_pulang' => $item->foto_pulang ? asset('storage/' . $item->foto_pulang) : null,
                    'status_kehadiran' => $item->status_kehadiran,
                    'total_jam_kerja' => $item->total_jam_kerja,
                    'total_jam_kerja_formatted' => $this->formatJamKerja($item->total_jam_kerja),
                ];
            });

        return $logs;
    }

    /**
     * Get summary presensi bulanan
     */
    private function getSummaryBulanan($employeeId, $bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $rekaps = RekapPresensiHarian::where('employee_id', $employeeId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        // Hitung hari kerja dalam bulan (exclude weekend)
        $totalHariKerja = 0;
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend()) {
                $totalHariKerja++;
            }
            $currentDate->addDay();
        }

        $totalHadir = $rekaps->where('status_kehadiran', 'hadir')->count();
        $totalTerlambat = $rekaps->where('status_kehadiran', 'terlambat')->count();
        $totalAlpha = $totalHariKerja - $rekaps->count();
        $totalIzin = $rekaps->where('status_kehadiran', 'izin')->count();
        $totalSakit = $rekaps->where('status_kehadiran', 'sakit')->count();
        $totalTidakValid = $rekaps->where('status_kehadiran', 'tidak_valid')->count();

        // Hitung total jam kerja
        $totalJamKerja = $rekaps->sum('total_jam_kerja');
        $rataRataJamKerja = $rekaps->where('total_jam_kerja', '>', 0)->avg('total_jam_kerja');

        return [
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'bulan_nama' => Carbon::create($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_selesai' => $endDate->format('Y-m-d'),
            ],
            'kehadiran' => [
                'total_hari_kerja' => $totalHariKerja,
                'total_presensi' => $rekaps->count(),
                'hadir' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'alpha' => $totalAlpha,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
                'tidak_valid' => $totalTidakValid,
                'persentase_kehadiran' => $totalHariKerja > 0 
                    ? round(($rekaps->count() / $totalHariKerja) * 100, 2) 
                    : 0,
            ],
            'jam_kerja' => [
                'total_menit' => $totalJamKerja,
                'total_formatted' => $this->formatJamKerja($totalJamKerja),
                'rata_rata_menit' => round($rataRataJamKerja, 0),
                'rata_rata_formatted' => $this->formatJamKerja($rataRataJamKerja),
            ],
        ];
    }

    /**
     * Format menit ke jam dan menit
     */
    private function formatJamKerja($menit)
    {
        if (!$menit) return '0 jam 0 menit';

        $jam = floor($menit / 60);
        $sisaMenit = $menit % 60;

        return $jam . ' jam ' . $sisaMenit . ' menit';
    }

    /**
     * Get riwayat presensi untuk periode tertentu
     */
    public function riwayat(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:hadir,terlambat,alpha,izin,sakit,tidak_valid',
            'jenis_presensi' => 'nullable|in:masuk,pulang',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        $query = Presensi::where('employee_id', $request->employee_id)
            ->with(['perusahaan', 'divisi']);

        // Filter tanggal
        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_presensi', [
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ]);
        } elseif ($request->tanggal_mulai) {
            $query->where('tanggal_presensi', '>=', $request->tanggal_mulai);
        } elseif ($request->tanggal_selesai) {
            $query->where('tanggal_presensi', '<=', $request->tanggal_selesai);
        } else {
            // Default 30 hari terakhir
            $query->where('tanggal_presensi', '>=', Carbon::now()->subDays(30));
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter jenis presensi
        if ($request->jenis_presensi) {
            $query->where('jenis_presensi', $request->jenis_presensi);
        }

        // Order by terbaru
        $query->orderBy('tanggal_presensi', 'desc')
              ->orderBy('waktu_presensi', 'desc');

        // Pagination
        $perPage = $request->per_page ?? 15;
        $riwayat = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $riwayat->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal_presensi' => $item->tanggal_presensi,
                    'tanggal_formatted' => Carbon::parse($item->tanggal_presensi)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                    'jenis_presensi' => $item->jenis_presensi,
                    'waktu_presensi' => $item->waktu_presensi,
                    'waktu_formatted' => Carbon::parse($item->waktu_presensi)->format('H:i'),
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'akurasi_gps' => $item->akurasi_gps,
                    'jarak_dari_lokasi' => $item->jarak_dari_lokasi,
                    'foto_presensi' => $item->foto_presensi ? asset('storage/' . $item->foto_presensi) : null,
                    'status' => $item->status,
                    'perusahaan' => $item->perusahaan?->nama_perusahaan,
                    'divisi' => $item->divisi?->nama_divisi,
                ];
            }),
            'pagination' => [
                'total' => $riwayat->total(),
                'per_page' => $riwayat->perPage(),
                'current_page' => $riwayat->currentPage(),
                'last_page' => $riwayat->lastPage(),
                'from' => $riwayat->firstItem(),
                'to' => $riwayat->lastItem(),
            ],
        ]);
    }

    /**
     * Export presensi ke Excel/PDF
     */
    public function export(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'format' => 'required|in:excel,pdf',
        ]);

        // TODO: Implementasi export Excel/PDF
        // Bisa pakai Laravel Excel atau DomPDF

        return response()->json([
            'success' => true,
            'message' => 'Export sedang diproses',
        ]);
    }
}