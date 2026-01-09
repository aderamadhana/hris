<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeEmployment;

class PresensiLogController extends Controller
{
    public function logHarian(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'tanggal'     => ['nullable', 'date_format:Y-m-d'],
            'bulan'       => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun'       => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $employeeId = (int) $validated['employee_id'];

        // Basic guard employee exists (sesuaikan nama tabel kalau beda)
        $employee = DB::table('employees')
            ->where('id', $employeeId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan',
            ], 404);
        }

        // MODE 1: Summary bulanan (dashboard)
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $bulan = (int) $request->bulan;
            $tahun = (int) $request->tahun;

            $summary = $this->buildMonthlySummary($employeeId, $bulan, $tahun);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                ],
            ]);
        }

        // MODE 2: Log harian
        $tz = config('app.timezone', 'Asia/Jakarta');
        $tanggal = $request->filled('tanggal')
            ? Carbon::createFromFormat('Y-m-d', $request->tanggal, $tz)
            : Carbon::now($tz);

        $data = $this->buildDailyLog($employee, $tanggal);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function buildDailyLog(object $employee, Carbon $tanggal): array
    {
        $dateStr = $tanggal->format('Y-m-d');

        // REKAP (1 row)
        $rekap = DB::table('rekap_presensi_harian')
            ->where('employee_id', $employee->id)
            ->where('tanggal', $dateStr)
            ->first();

        // DETAIL presensi (N rows)
        $detail = DB::table('presensi')
            ->where('employee_id', $employee->id)
            ->where('tanggal_presensi', $dateStr)
            ->orderBy('waktu_presensi', 'asc')
            ->get();

        // Tentukan shift_id (prioritas: rekap -> employee -> detail pertama)
        $shiftId =
            ($rekap->shift_id ?? null) ??
            ($employee->shift_id ?? null) ??
            ($detail->first()->shift_id ?? null);

        // Ambil shift row jika ada
        $shiftRow = null;
        if ($shiftId) {
            $shiftRow = DB::table('shift')->where('id', $shiftId)->first();
        }

        $shiftInfo = $this->formatShiftInfo($shiftRow);

        // Map detail presensi
        $detailMapped = $detail->map(function ($item) {
            return [
                'id' => $item->id,
                'jenis_presensi' => $item->jenis_presensi,
                'waktu_presensi' => $item->waktu_presensi,
                'waktu_formatted' => $item->waktu_presensi ? Carbon::parse($item->waktu_presensi)->format('H:i') : null,

                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'akurasi_gps' => $item->akurasi_gps,
                'jarak_dari_lokasi' => $item->jarak_dari_lokasi,
                'is_valid_location' => $item->is_valid_location,

                'foto_presensi' => $this->fotoUrl($item->foto_presensi),
                'status' => $item->status,
                'keterangan' => $item->keterangan,

                'device_info' => $item->device_info,
                'ip_address' => $item->ip_address,
            ];
        })->values();

        // Map rekap (jangan lempar raw object biar stabil untuk FE)
        $rekapMapped = null;
        if ($rekap) {
            $rekapMapped = [
                'id' => $rekap->id,
                'employee_id' => $rekap->employee_id,
                'shift_id' => $rekap->shift_id,
                'perusahaan_id' => $rekap->perusahaan_id,
                'divisi_id' => $rekap->divisi_id,
                'tanggal' => $rekap->tanggal,

                'waktu_masuk' => $rekap->waktu_masuk,
                'waktu_pulang' => $rekap->waktu_pulang,
                'foto_masuk' => $this->fotoUrl($rekap->foto_masuk),
                'foto_pulang' => $this->fotoUrl($rekap->foto_pulang),

                'lat_masuk' => $rekap->lat_masuk,
                'long_masuk' => $rekap->long_masuk,
                'valid_lokasi_masuk' => $rekap->valid_lokasi_masuk,

                'lat_pulang' => $rekap->lat_pulang,
                'long_pulang' => $rekap->long_pulang,
                'valid_lokasi_pulang' => $rekap->valid_lokasi_pulang,

                'status_kehadiran' => $rekap->status_kehadiran,
                'total_jam_kerja' => $rekap->total_jam_kerja,
                'durasi_kerja_menit' => $rekap->durasi_kerja_menit,
                'keterangan' => $rekap->keterangan,
            ];
        }

        $sudahMasuk = $detail->where('jenis_presensi', 'masuk')->isNotEmpty();
        $sudahPulang = $detail->where('jenis_presensi', 'pulang')->isNotEmpty();

        return [
            'tanggal' => $dateStr,
            'tanggal_formatted' => $tanggal->copy()->locale('id')->isoFormat('dddd, D MMMM YYYY'),

            // shift jangan null (kalau shiftRow null => placeholder)
            'shift' => $shiftInfo,

            'rekap' => $rekapMapped,
            'detail' => $detailMapped,

            'status' => [
                'sudah_masuk' => $sudahMasuk,
                'sudah_pulang' => $sudahPulang,
            ],
        ];
    }

    private function buildMonthlySummary(int $employeeId, int $bulan, int $tahun): array
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        $start = Carbon::create($tahun, $bulan, 1, 0, 0, 0, $tz)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $rekaps = DB::table('rekap_presensi_harian')
            ->where('employee_id', $employeeId)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();

        $totalHariKerja = $rekaps->count();

        $hadirTepat = 0;
        $terlambat = 0;

        foreach ($rekaps as $r) {
            // kalau tidak ada waktu_masuk, skip dari hitungan tepat/terlambat
            if (empty($r->waktu_masuk)) {
                continue;
            }

            // jika status_kehadiran sudah eksplisit, pakai itu dulu
            $status = strtolower((string) ($r->status_kehadiran ?? ''));

            if (str_contains($status, 'terlambat') || str_contains($status, 'late')) {
                $terlambat++;
                continue;
            }

            if (str_contains($status, 'hadir') || str_contains($status, 'tepat')) {
                $hadirTepat++;
                continue;
            }

            // fallback: bandingkan dengan jam_masuk shift bila ada & tidak fleksibel
            $shift = null;
            if (!empty($r->shift_id)) {
                $shift = DB::table('shift')->where('id', $r->shift_id)->first();
            }

            if ($shift) {
                $isFlexible = strtoupper((string) $shift->kode_shift) === 'FLEX'
                    || stripos((string) $shift->nama_shift, 'fleksibel') !== false;

                if ($isFlexible) {
                    $hadirTepat++;
                } else {
                    $jamMasukShift = $shift->jam_masuk ? Carbon::parse($shift->jam_masuk) : null;
                    $jamMasukAktual = Carbon::parse($r->waktu_masuk);

                    if ($jamMasukShift && $jamMasukAktual->gt($jamMasukShift)) {
                        $terlambat++;
                    } else {
                        $hadirTepat++;
                    }
                }
            } else {
                // tanpa shift, anggap hadir (biar tidak 0 terus)
                $hadirTepat++;
            }
        }

        $persentase = $totalHariKerja > 0
            ? round(($hadirTepat / $totalHariKerja) * 100, 2)
            : 0;

        return [
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            'kehadiran' => [
                'hadir' => $hadirTepat,
                'terlambat' => $terlambat,
                'total_hari_kerja' => $totalHariKerja,
                'persentase_kehadiran' => $persentase,
            ],
        ];
    }

    private function formatShiftInfo(?object $shiftRow): array
    {
        if (!$shiftRow) {
            // placeholder supaya FE tidak perlu handle null
            return [
                'nama_shift' => 'Shift tidak tersedia',
                'kode_shift' => null,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'is_flexible' => false,
            ];
        }

        $isFlexible = strtoupper((string) $shiftRow->kode_shift) === 'FLEX'
            || stripos((string) $shiftRow->nama_shift, 'fleksibel') !== false;

        return [
            'nama_shift' => $shiftRow->nama_shift,
            'kode_shift' => $shiftRow->kode_shift,
            'jam_masuk' => $isFlexible
                ? 'Fleksibel'
                : ($shiftRow->jam_masuk ? Carbon::parse($shiftRow->jam_masuk)->format('H:i') : null),
            'jam_pulang' => $isFlexible
                ? 'Fleksibel'
                : ($shiftRow->jam_pulang ? Carbon::parse($shiftRow->jam_pulang)->format('H:i') : null),
            'is_flexible' => $isFlexible,
        ];
    }

    private function fotoUrl(?string $path): ?string
    {
        if (!$path) return null;

        // kalau sudah URL, biarkan
        if (preg_match('/^https?:\/\//i', $path)) return $path;

        // kalau sudah ada "storage/", biarkan sekali
        $clean = ltrim($path, '/');
        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        return asset('storage/' . $clean);
    }

    public function summary(Request $request)
    {
        $employeeId = $request->query('employee_id');

        if (!$employeeId) {
            return response()->json([
                'message' => 'employee_id wajib diisi'
            ], 422);
        }

        $employee = Employee::query()->findOrFail($employeeId);

        $base = EmployeeEmployment::query()
            ->where('employee_id', $employee->id);

        $historyTotal = (clone $base)->count();

        // kontrak aktif
        $current = (clone $base)
            ->whereRaw('LOWER(TRIM(status)) = ?', ['aktif'])
            ->orderByDesc('tgl_awal_kerja')
            ->orderByDesc('created_at')
            ->first();

        if (!$current) {
            $current = (clone $base)
                ->orderByDesc('tgl_awal_kerja')
                ->orderByDesc('created_at')
                ->first();
        }

        // ambil semua riwayat (urut terbaru dulu)
        $histories = (clone $base)
            ->orderByDesc('tgl_awal_kerja')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'perusahaan' => $h->perusahaan,
                    'jabatan' => $h->jabatan,
                    'penempatan' => $h->penempatan,
                    'no_kontrak' => $h->no_kontrak,
                    'cost_center' => $h->cost_center,
                    'tgl_daftar' => $h->tgl_daftar,
                    'keterangan_status' => $h->keterangan_status,
                    'job_roll' => $h->job_roll,
                    'masa_kerja' => $h->masa_kerja,
                    'pola_kerja' => $h->pola_kerja,
                    'jenis_kerja' => $h->jenis_kerja,
                    'hari_kerja' => $h->hari_kerja,
                    'tgl_awal_kerja' => $h->tgl_awal_kerja,
                    'tgl_akhir_kerja' => $h->tgl_akhir_kerja,
                    'tgl_awal_kerja_label' => $this->formatDateOrNull($h->tgl_awal_kerja),
                    'tgl_akhir_kerja_label' => $this->formatDateOrNull($h->tgl_akhir_kerja),
                    'jenis_kontrak' => $h->jenis_kontrak,
                    'status' => $h->status,
                    'created_at' => optional($h->created_at)->toDateTimeString(),
                    'updated_at' => optional($h->updated_at)->toDateTimeString(),
                ];
            });

        $endRaw = $current?->tgl_akhir_kerja;
        $endLabel = $this->formatDateOrNull($endRaw);

        return response()->json([
            'employee_id' => (int) $employee->id,

            // summary untuk card
            'contract_type' => $current?->jenis_kontrak,
            'contract_end_date' => $endRaw,
            'contract_end_date_label' => $endLabel,
            'history_total' => $historyTotal,

            // untuk modal
            'histories' => $histories,

            // optional
            'current' => $current,
        ]);
    }


    private function formatDateOrNull($value): ?string
    {
        if ($value === null) return null;

        $v = trim((string) $value);
        if ($v === '' || $v === '-' || strtolower($v) === 'null') return null;

        try {
            return Carbon::parse($v)->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            // Kalau format tanggal kamu bukan parseable, jangan dipaksa
            return $v;
        }
    }
}
