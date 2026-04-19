<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeEmployment;
use App\Models\RekapPresensiHarian;
use App\Models\Presensi;

class PresensiLogController extends Controller
{
    /**
     * Timezone default untuk seluruh controller.
     */
    private const TZ = 'Asia/Jakarta';

    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $page    = (int) $request->get('page', 1);

        $query = RekapPresensiHarian::query()->with([
            'employee:id,nama',
            'employee.employments:id,employee_id,perusahaan,jabatan,penempatan',
            'shift:id,nama_shift,jam_masuk,jam_pulang',
        ]);

        // filter tanggal absen
        // ✅ FIX: parse dengan timezone WIB
        if ($request->filled('filtered_tanggal_absen')) {
            $date = Carbon::parse($request->filtered_tanggal_absen, self::TZ)->toDateString();
            $query->whereDate('tanggal', $date);
        }

        // filter perusahaan
        if ($request->filled('filtered_perusahaan')) {
            $perusahaanId = (int) $request->filtered_perusahaan;
            $query->where('perusahaan_id', $perusahaanId);
        }

        // filter divisi/jabatan
        if ($request->filled('filtered_jabatan')) {
            $divisiId = (int) $request->filtered_jabatan;
            $query->where('divisi_id', $divisiId);
        }

        // status
        if ($request->filled('status')) {
            $query->where('status_kehadiran', $request->status);
        }

        // search bebas
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('keterangan', 'like', "%{$s}%")
                    ->orWhere('status_kehadiran', 'like', "%{$s}%")
                    ->orWhereHas('employee', function ($qe) use ($s) {
                        $qe->where('nama', 'like', "%{$s}%");
                    })
                    ->orWhereHas('employee.employments', function ($qx) use ($s) {
                        $qx->where('perusahaan', 'like', "%{$s}%")
                            ->orWhere('jabatan', 'like', "%{$s}%")
                            ->orWhere('penempatan', 'like', "%{$s}%");
                    });
            });
        }

        $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        $records = $query->paginate($perPage, ['*'], 'page', $page);

        // ✅ Helper: ekstrak H:i:s dari berbagai format kolom TIME / DATETIME / string
        $extractTime = function ($t): ?string {
            if (!$t) return null;
            if ($t instanceof \DateTimeInterface) return $t->format('H:i:s');
            $s = trim((string) $t);
            if (preg_match('/(\d{2}:\d{2}:\d{2})/', $s, $m)) return $m[1];
            if (preg_match('/(\d{2}:\d{2})/', $s, $m)) return $m[1] . ':00';
            return null;
        };

        // ✅ FIX: pakai self::TZ konsisten
        $computeWorkMinutes = function ($tanggalYmd, $inValue, $outValue) use ($extractTime): ?int {
            $in  = $extractTime($inValue);
            $out = $extractTime($outValue);
            if (!$tanggalYmd || !$in || !$out) return null;

            $tz    = self::TZ;
            $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$tanggalYmd} {$in}", $tz);
            $end   = Carbon::createFromFormat('Y-m-d H:i:s', "{$tanggalYmd} {$out}", $tz);
            if ($end->lessThan($start)) $end->addDay();

            $diff = $start->diffInMinutes($end, false);
            return $diff >= 0 ? $diff : null;
        };

        $minutesToHHmmSafe = function ($minutes): ?string {
            if ($minutes === null || !is_numeric($minutes)) return null;
            $m = (int) round((float) $minutes);
            if ($m < 0) return null;
            return sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
        };

        $durationLabel = function ($clockOut, $minutes): ?string {
            if (!$clockOut) return 'Belum clock-out';
            if ($minutes === null) return 'Durasi tidak valid';
            return null;
        };

        $data = $records->getCollection()->map(function ($p) use (
            $extractTime,
            $computeWorkMinutes,
            $minutesToHHmmSafe,
            $durationLabel
        ) {
            // ✅ FIX: parse tanggal dengan timezone WIB
            $tanggalYmd = $p->tanggal
                ? Carbon::parse($p->tanggal, self::TZ)->format('Y-m-d')
                : null;

            $clockIn  = $extractTime($p->waktu_masuk);
            $clockOut = $extractTime($p->waktu_pulang);

            $computedMinutes = $computeWorkMinutes($tanggalYmd, $p->waktu_masuk, $p->waktu_pulang);
            $dbMinutes       = (is_numeric($p->total_jam_kerja) && (int) $p->total_jam_kerja >= 0)
                ? (int) $p->total_jam_kerja
                : null;
            $totalMinutes = $computedMinutes ?? $dbMinutes;

            $hhmm  = $minutesToHHmmSafe($totalMinutes);
            $label = $durationLabel($clockOut, $totalMinutes);

            $employment = $p->employee->employments ?? null;

            return [
                'id'     => $p->id,
                // ✅ FIX: parse tanggal dengan timezone WIB
                'tanggal' => $p->tanggal
                    ? Carbon::parse($p->tanggal, self::TZ)->format('d/m/Y')
                    : '-',

                'nama_perusahaan' => $employment->perusahaan ?? '-',
                'nama_divisi'     => $employment->penempatan ?? '-',
                'jabatan'         => $employment->jabatan    ?? '-',

                'nama_karyawan'    => $p->employee->nama ?? '-',
                'status_kehadiran' => $p->status_kehadiran ?? '-',
                'keterangan'       => $p->keterangan,

                'data_presensi' => [
                    'clock_in'       => $clockIn,
                    'clock_out'      => $clockOut,
                    'foto_masuk_url' => $p->foto_masuk  ? Storage::url($p->foto_masuk)  : null,
                    'foto_pulang_url'=> $p->foto_pulang ? Storage::url($p->foto_pulang) : null,

                    'jam_masuk'  => $p->shift ? $extractTime($p->shift->jam_masuk)  : null,
                    'jam_pulang' => $p->shift ? $extractTime($p->shift->jam_pulang) : null,
                    'nama_shift' => $p->shift->nama_shift ?? '-',

                    'total_jam_kerja_menit' => $totalMinutes,
                    'total_jam_kerja_hhmm'  => $hhmm,
                    'durasi_label'          => $label,
                ],
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total'        => $records->total(),
                'per_page'     => $records->perPage(),
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
            ],
        ]);
    }

    public function logHarian(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'tanggal'     => ['nullable', 'date_format:Y-m-d'],
            'bulan'       => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun'       => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $employeeId = (int) $validated['employee_id'];

        $employee = DB::table('employees')
            ->where('id', $employeeId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan',
            ], 404);
        }

        // MODE 1: Summary bulanan
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $bulan   = (int) $request->bulan;
            $tahun   = (int) $request->tahun;
            $summary = $this->buildMonthlySummary($employeeId, $bulan, $tahun);

            return response()->json([
                'success' => true,
                'data'    => ['summary' => $summary],
            ]);
        }

        // MODE 2: Log harian
        // ✅ FIX: selalu pakai self::TZ, bukan config() yang bisa saja UTC
        $tz      = self::TZ;
        $tanggal = $request->filled('tanggal')
            ? Carbon::createFromFormat('Y-m-d', $request->tanggal, $tz) // ✅
            : Carbon::now($tz);                                          // ✅

        $data = $this->buildDailyLog($employee, $tanggal);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    private function buildDailyLog(object $employee, Carbon $tanggal): array
    {
        $dateStr = $tanggal->format('Y-m-d');

        $rekap = DB::table('rekap_presensi_harian')
            ->where('employee_id', $employee->id)
            ->where('tanggal', $dateStr)
            ->first();

        $detail = DB::table('presensi')
            ->where('employee_id', $employee->id)
            ->where('tanggal_presensi', $dateStr)
            ->orderBy('waktu_presensi', 'asc')
            ->get();

        $shiftId =
            ($rekap->shift_id   ?? null) ??
            ($employee->shift_id ?? null) ??
            ($detail->first()->shift_id ?? null);

        $shiftRow = null;
        if ($shiftId) {
            $shiftRow = DB::table('shift')->where('id', $shiftId)->first();
        }

        $shiftInfo = $this->formatShiftInfo($shiftRow);

        // ✅ FIX: waktu_formatted pakai getRawOriginal-style + pattern check + TZ
        $tz = self::TZ;
        $detailMapped = $detail->map(function ($item) use ($tz) {
            $waktuFormatted = null;
            try {
                $raw = $item->waktu_presensi;
                if ($raw) {
                    if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $raw)) {
                        // Kolom TIME murni
                        $waktuFormatted = Carbon::today($tz)
                            ->setTimeFromTimeString($raw)
                            ->format('H:i');
                    } else {
                        $waktuFormatted = Carbon::parse($raw, $tz)->format('H:i'); // ✅
                    }
                }
            } catch (\Throwable $e) {
                $waktuFormatted = (string) ($item->waktu_presensi ?? '-');
            }

            return [
                'id'                => $item->id,
                'jenis_presensi'    => $item->jenis_presensi,
                'waktu_presensi'    => $item->waktu_presensi,
                'waktu_formatted'   => $waktuFormatted,

                'latitude'          => $item->latitude,
                'longitude'         => $item->longitude,
                'akurasi_gps'       => $item->akurasi_gps,
                'jarak_dari_lokasi' => $item->jarak_dari_lokasi,
                'is_valid_location' => $item->is_valid_location,

                'foto_presensi'     => $this->fotoUrl($item->foto_presensi),
                'status'            => $item->status,
                'keterangan'        => $item->keterangan,

                'device_info'       => $item->device_info,
                'ip_address'        => $item->ip_address,
            ];
        })->values();

        $rekapMapped = null;
        if ($rekap) {
            $rekapMapped = [
                'id'           => $rekap->id,
                'employee_id'  => $rekap->employee_id,
                'shift_id'     => $rekap->shift_id,
                'perusahaan_id'=> $rekap->perusahaan_id,
                'divisi_id'    => $rekap->divisi_id,
                'tanggal'      => $rekap->tanggal,

                'waktu_masuk'  => $rekap->waktu_masuk,
                'waktu_pulang' => $rekap->waktu_pulang,
                'foto_masuk'   => $this->fotoUrl($rekap->foto_masuk),
                'foto_pulang'  => $this->fotoUrl($rekap->foto_pulang),

                'lat_masuk'           => $rekap->lat_masuk,
                'long_masuk'          => $rekap->long_masuk,
                'valid_lokasi_masuk'  => $rekap->valid_lokasi_masuk,

                'lat_pulang'          => $rekap->lat_pulang,
                'long_pulang'         => $rekap->long_pulang,
                'valid_lokasi_pulang' => $rekap->valid_lokasi_pulang,

                'status_kehadiran'   => $rekap->status_kehadiran,
                'total_jam_kerja'    => $rekap->total_jam_kerja,
                'durasi_kerja_menit' => $rekap->durasi_kerja_menit,
                'keterangan'         => $rekap->keterangan,
            ];
        }

        $sudahMasuk  = $detail->where('jenis_presensi', 'masuk')->isNotEmpty();
        $sudahPulang = $detail->where('jenis_presensi', 'pulang')->isNotEmpty();

        return [
            'tanggal'           => $dateStr,
            'tanggal_formatted' => $tanggal->copy()->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'shift'             => $shiftInfo,
            'rekap'             => $rekapMapped,
            'detail'            => $detailMapped,
            'status'            => [
                'sudah_masuk'  => $sudahMasuk,
                'sudah_pulang' => $sudahPulang,
            ],
        ];
    }

    private function buildMonthlySummary(int $employeeId, int $bulan, int $tahun): array
    {
        $tz    = self::TZ; // ✅ FIX: pakai const, bukan config()
        $start = Carbon::create($tahun, $bulan, 1, 0, 0, 0, $tz)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $rekaps = DB::table('rekap_presensi_harian')
            ->where('employee_id', $employeeId)
            ->whereBetween('tanggal', [
                $start->toDateString(), // ✅ date string agar query kolom DATE akurat
                $end->toDateString(),
            ])
            ->get();

        $totalHariKerja = $rekaps->count();
        $hadirTepat     = 0;
        $terlambat      = 0;

        foreach ($rekaps as $r) {
            if (empty($r->waktu_masuk)) {
                continue;
            }

            $status = strtolower((string) ($r->status_kehadiran ?? ''));

            if (str_contains($status, 'terlambat') || str_contains($status, 'late')) {
                $terlambat++;
                continue;
            }

            if (str_contains($status, 'hadir') || str_contains($status, 'tepat')) {
                $hadirTepat++;
                continue;
            }

            // fallback: bandingkan dengan jam_masuk shift
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
                    // ✅ FIX: parse dengan timezone WIB
                    $jamMasukShift  = $shift->jam_masuk
                        ? Carbon::parse($shift->jam_masuk, $tz)
                        : null;
                    $jamMasukAktual = Carbon::parse($r->waktu_masuk, $tz); // ✅

                    if ($jamMasukShift && $jamMasukAktual->gt($jamMasukShift)) {
                        $terlambat++;
                    } else {
                        $hadirTepat++;
                    }
                }
            } else {
                $hadirTepat++;
            }
        }

        $persentase = $totalHariKerja > 0
            ? round(($hadirTepat / $totalHariKerja) * 100, 2)
            : 0;

        return [
            'periode'   => [
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            'kehadiran' => [
                'hadir'                => $hadirTepat,
                'terlambat'            => $terlambat,
                'total_hari_kerja'     => $totalHariKerja,
                'persentase_kehadiran' => $persentase,
            ],
        ];
    }

    private function formatShiftInfo(?object $shiftRow): array
    {
        if (!$shiftRow) {
            return [
                'id_shift'   => null,
                'nama_shift' => 'Shift tidak tersedia',
                'kode_shift' => null,
                'jam_masuk'  => null,
                'jam_pulang' => null,
                'is_flexible'=> false,
            ];
        }

        $isFlexible = strtoupper((string) $shiftRow->kode_shift) === 'FLEX'
            || stripos((string) $shiftRow->nama_shift, 'fleksibel') !== false;

        return [
            'id_shift'    => $shiftRow->id,
            'nama_shift'  => $shiftRow->nama_shift,
            'kode_shift'  => $shiftRow->kode_shift,
            // ✅ FIX: parse jam shift dengan timezone WIB
            'jam_masuk'   => $isFlexible
                ? 'Fleksibel'
                : ($shiftRow->jam_masuk
                    ? Carbon::parse($shiftRow->jam_masuk, self::TZ)->format('H:i')
                    : null),
            'jam_pulang'  => $isFlexible
                ? 'Fleksibel'
                : ($shiftRow->jam_pulang
                    ? Carbon::parse($shiftRow->jam_pulang, self::TZ)->format('H:i')
                    : null),
            'is_flexible' => $isFlexible,
        ];
    }

    private function fotoUrl(?string $path): ?string
    {
        if (!$path) return null;

        if (preg_match('/^https?:\/\//i', $path)) return $path;

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
                'message' => 'employee_id wajib diisi',
            ], 422);
        }

        $employee = Employee::query()->findOrFail($employeeId);

        $base = EmployeeEmployment::query()
            ->where('employee_id', $employee->id);

        $historyTotal = (clone $base)->count();

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

        $histories = (clone $base)
            ->orderByDesc('tgl_awal_kerja')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                return [
                    'id'                    => $h->id,
                    'perusahaan'            => $h->perusahaan,
                    'jabatan'               => $h->jabatan,
                    'penempatan'            => $h->penempatan,
                    'no_kontrak'            => $h->no_kontrak,
                    'cost_center'           => $h->cost_center,
                    'tgl_daftar'            => $h->tgl_daftar,
                    'keterangan_status'     => $h->keterangan_status,
                    'job_roll'              => $h->job_roll,
                    'masa_kerja'            => $h->masa_kerja,
                    'pola_kerja'            => $h->pola_kerja,
                    'jenis_kerja'           => $h->jenis_kerja,
                    'hari_kerja'            => $h->hari_kerja,
                    'tgl_awal_kerja'        => $h->tgl_awal_kerja,
                    'tgl_akhir_kerja'       => $h->tgl_akhir_kerja,
                    'tgl_awal_kerja_label'  => $this->formatDateOrNull($h->tgl_awal_kerja),
                    'tgl_akhir_kerja_label' => $this->formatDateOrNull($h->tgl_akhir_kerja),
                    'jenis_kontrak'         => $h->jenis_kontrak,
                    'status'                => $h->status,
                    'created_at'            => optional($h->created_at)->toDateTimeString(),
                    'updated_at'            => optional($h->updated_at)->toDateTimeString(),
                ];
            });

        $endRaw   = $current?->tgl_akhir_kerja;
        $endLabel = $this->formatDateOrNull($endRaw);

        return response()->json([
            'employee_id'              => (int) $employee->id,
            'contract_type'            => $current?->jenis_kontrak,
            'contract_end_date'        => $endRaw,
            'contract_end_date_label'  => $endLabel,
            'history_total'            => $historyTotal,
            'histories'                => $histories,
            'current'                  => $current,
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
            return $v;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_kehadiran' => 'required|in:valid,tidak_valid,hadir,izin,sakit,alpha',
        ]);

        DB::beginTransaction();

        try {
            $rekap = RekapPresensiHarian::findOrFail($id);
            $rekap->status_kehadiran = $request->status_kehadiran;
            $rekap->save();

            Presensi::where('employee_id', $rekap->employee_id)
                ->where('tanggal_presensi', $rekap->tanggal)
                ->update(['status' => $request->status_kehadiran]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diubah',
                'data'    => $rekap,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status kehadiran',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
