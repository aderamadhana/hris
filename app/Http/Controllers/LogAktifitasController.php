<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Divisi;
use App\Models\Perusahaan;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\LogAktifitas;
use App\Models\ImportLogAktifitas;
use App\Models\Employee;
use App\Models\Aktifitas;
use Illuminate\Support\Facades\DB;
use App\Exports\LogAktifitasExport;
use App\Imports\AktifitasImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LogAktifitasController extends Controller
{
    public function index(Request $request)
    {
        $search              = trim((string) $request->query('search'));
        $status              = $request->query('status'); // reserved
        $jabatanId           = $request->query('filtered_jabatan');     // ID
        $perusahaanId        = $request->query('filtered_perusahaan');  // ID
        $tanggalDari   = $request->query('filtered_tanggal_dari');
        $tanggalSampai = $request->query('filtered_tanggal_sampai');
        $employeeId = $request->query('employee_id');
        $perPage             = min((int) $request->query('per_page', 10), 100);

        $jabatanName = null;
        $perusahaanName = null;

        if ($jabatanId) {
            $jabatanName = Divisi::where('id', $jabatanId)->value('nama_divisi');
        }

        if ($perusahaanId) {
            $perusahaanName = Perusahaan::where('id', $perusahaanId)->value('nama_perusahaan');
        }

        $query = LogAktifitas::query()
            ->with([
                'employee:id,nama',
                'employee.currentEmployment:id,employee_id,perusahaan,penempatan,jabatan',
            ])
            ->orderByDesc('tgl')
            ->orderByDesc('created_at');
        
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_kerja', 'like', "%{$search}%")
                ->orWhereHas('employee', function ($qe) use ($search) {
                    $qe->where('nama', 'like', "%{$search}%");
                });
            });
        }

        if ($tanggalDari && $tanggalSampai) {
            $query->whereBetween('tgl', [$tanggalDari, $tanggalSampai]);
        } elseif ($tanggalDari) {
            $query->whereDate('tgl', '>=', $tanggalDari);
        } elseif ($tanggalSampai) {
            $query->whereDate('tgl', '<=', $tanggalSampai);
        }

        if ($jabatanName) {
            $query->whereHas('employee.currentEmployment', function ($q) use ($jabatanName) {
                $q->where('penempatan', $jabatanName);
            });
        }

        if ($perusahaanName) {
            $query->whereHas('employee.currentEmployment', function ($q) use ($perusahaanName) {
                $q->where('perusahaan', $perusahaanName);
            });
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($item) {
            $employment = $item->employee->currentEmployment;

            return [
                'id' => $item->id,

                'tanggal' => optional($item->tgl)->format('d M Y'),
                'tanggal_aktifitas' => optional($item->tgl)->format('Y-m-d'),
                'id_karyawan' => $item->employee->id ?? '-',

                'nama_karyawan' => $item->employee->nama ?? '-',

                'nama_perusahaan' => $employment->perusahaan ?? '-',
                'nama_divisi'     => $employment->penempatan ?? '-',
                'rincian'     => $item,

                'data_aktifitas' => [
                    'kode_kerja' => $item->kode_kerja,
                    'nama_shift' => $item->shift,

                    'jam_masuk' => $item->jam_masuk
                        ? Carbon::parse($item->jam_masuk)->format('H:i:s')
                        : null,

                    'jam_pulang' => $item->jam_pulang
                        ? Carbon::parse($item->jam_pulang)->format('H:i:s')
                        : null,

                    'total_jam_kerja_hhmm' => $item->jam_kerja_menit
                        ? sprintf(
                            '%02d:%02d',
                            intdiv($item->jam_kerja_menit, 60),
                            $item->jam_kerja_menit % 60
                        )
                        : null,

                    'hasil_kerja'  => $item->hasil_kerja,
                    'hasil_lembur' => $item->hasil_lembur,
                    'total_act'    => $item->total_act,
                ],
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
            ],
        ]);
    }

    public function recentActivities(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $limit = (int) $request->query('limit', 5);

        if (!$employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'employee_id wajib diisi',
            ], 422);
        }

        // Guard limit
        $limit = min(max($limit, 1), 20);

        $activities = LogAktifitas::query()
            ->where('employee_id', $employeeId)
            ->orderByDesc('tgl')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get([
                'id',
                'employee_id',
                'aktifitas_id',
                'tgl',
                'shift',
                'kode_kerja',
                'jam_masuk',
                'jam_pulang',
                'hasil_kerja',
                'hasil_lembur',
                'return_qty',
                'tolak_qc',
                'upah_scf',
                'bantu_scf',
                'denda_scf',
                'total_scf',
                'upah_act',
                'upah_bantu_act',
                'return_act',
                'denda_act',
                'total_act',
                'ket',
                'created_at',
            ])
            ->map(function ($item) {
                return [
                    'id'              => $item->id,

                    // raw (untuk edit)
                    'tgl'             => optional($item->tgl)->format('Y-m-d'),
                    'aktifitas_id'    => $item->aktifitas_id,
                    'shift'           => $item->shift,
                    'jam_masuk' => $item->jam_masuk
                        ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i:s')
                        : null,

                    'jam_pulang' => $item->jam_pulang
                        ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i:s')
                        : null,
                    'ket'             => $item->ket,

                    // display
                    'tgl_formatted'   => optional($item->tgl)->format('d M Y'),
                    'time'            => optional($item->created_at)->format('H:i'),
                    'kode_kerja'      => $item->kode_kerja,

                    // metrics
                    'hasil_kerja'     => $item->hasil_kerja,
                    'hasil_lembur'    => $item->hasil_lembur,
                    'return_qty'      => $item->return_qty,
                    'tolak_qc'        => $item->tolak_qc,

                    // cost
                    'upah_scf'        => $item->upah_scf,
                    'bantu_scf'       => $item->bantu_scf,
                    'denda_scf'       => $item->denda_scf,
                    'total_scf'       => $item->total_scf,

                    'upah_act'        => $item->upah_act,
                    'upah_bantu_act'  => $item->upah_bantu_act,
                    'return_act'      => $item->return_act,
                    'denda_act'       => $item->denda_act,
                    'total_act'       => $item->total_act,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    public function storeActivities(Request $request)
    {
        $validated = $request->validate([
            'employee_id'     => ['required', 'exists:employees,id'],
            'aktifitas_id'    => ['required', 'exists:aktifitas,id'],
            'tgl'             => ['required', 'date'],
            'shift'           => ['required', 'integer'],

            'jam_masuk'       => ['nullable'],
            'jam_pulang'      => ['nullable'],
            'jam_kerja_menit' => ['nullable', 'integer', 'min:0'],

            'hasil_kerja'     => ['nullable', 'integer', 'min:0'],
            'hasil_lembur'    => ['nullable', 'integer', 'min:0'],
            'return_qty'      => ['nullable', 'integer', 'min:0'],
            'tolak_qc'        => ['nullable', 'integer', 'min:0'],

            'upah_scf'        => ['nullable', 'integer', 'min:0'],
            'bantu_scf'       => ['nullable', 'integer', 'min:0'],
            'denda_scf'       => ['nullable', 'integer', 'min:0'],
            'total_scf'       => ['nullable', 'integer', 'min:0'],

            'upah_act'        => ['nullable', 'integer', 'min:0'],
            'upah_bantu_act'  => ['nullable', 'integer', 'min:0'],
            'return_act'      => ['nullable', 'integer', 'min:0'],
            'denda_act'       => ['nullable', 'integer', 'min:0'],
            'total_act'       => ['nullable', 'integer', 'min:0'],

            'ket'             => ['nullable', 'string', 'max:500'],
        ]);

        $employee  = Employee::findOrFail($validated['employee_id']);
        $aktifitas = Aktifitas::findOrFail($validated['aktifitas_id']);

        $exists = LogAktifitas::where([
            'employee_id'  => $validated['employee_id'],
            'tgl'          => $validated['tgl'],
            'shift'        => $validated['shift'],
            'aktifitas_id' => $validated['aktifitas_id'],
        ])->exists();

        // if ($exists) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Log aktivitas untuk tanggal & shift ini sudah ada',
        //     ], 409);
        // }

        if($validated['jam_masuk'] == 'Fleksibel'){
            $validated['jam_masuk'] = null;
        }

        if($validated['jam_pulang'] == 'Fleksibel'){
            $validated['jam_pulang'] = null;
        }

        $log = LogAktifitas::create([
            'employee_id'     => $validated['employee_id'],
            'aktifitas_id'    => $validated['aktifitas_id'],
            'tgl'             => $validated['tgl'],
            'shift'           => $validated['shift'],

            // derived
            'nama'            => $employee->nama,
            'kode_kerja'      => $aktifitas->kode,

            'jam_masuk'       => $validated['jam_masuk'] ?? null,
            'jam_pulang'      => $validated['jam_pulang'] ?? null,
            'jam_kerja_menit' => $validated['jam_kerja_menit'] ?? null,

            'hasil_kerja'     => $validated['hasil_kerja'] ?? 0,
            'hasil_lembur'    => $validated['hasil_lembur'] ?? 0,
            'return_qty'      => $validated['return_qty'] ?? 0,
            'tolak_qc'        => $validated['tolak_qc'] ?? 0,

            'upah_scf'        => $validated['upah_scf'] ?? 0,
            'bantu_scf'       => $validated['bantu_scf'] ?? 0,
            'denda_scf'       => $validated['denda_scf'] ?? 0,
            'total_scf'       => $validated['total_scf'] ?? 0,

            'upah_act'        => $validated['upah_act'] ?? 0,
            'upah_bantu_act'  => $validated['upah_bantu_act'] ?? 0,
            'return_act'      => $validated['return_act'] ?? 0,
            'denda_act'       => $validated['denda_act'] ?? 0,
            'total_act'       => $validated['total_act'] ?? 0,

            'ket'             => $validated['ket'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id' => $log->id,
            ],
        ], 201);
    }

    public function updateActivities(Request $request, $id)
    {
    
        $log = LogAktifitas::findOrFail($id);

        $validated = $request->validate([
            'tgl'             => ['required', 'date'],
            'shift'           => ['required', 'integer'],
            'aktifitas_id'    => ['required', 'exists:aktifitas,id'],
            'employee_id'    => ['required', 'integer'],

            'jam_masuk'       => ['nullable', 'date_format:H:i:s'],
            'jam_pulang'      => ['nullable', 'date_format:H:i:s'],
            'jam_kerja_menit' => ['nullable', 'integer', 'min:0'],

            'hasil_kerja'     => ['nullable', 'integer', 'min:0'],
            'hasil_lembur'    => ['nullable', 'integer', 'min:0'],
            'return_qty'      => ['nullable', 'integer', 'min:0'],
            'tolak_qc'        => ['nullable', 'integer', 'min:0'],

            'upah_scf'        => ['nullable', 'integer', 'min:0'],
            'bantu_scf'       => ['nullable', 'integer', 'min:0'],
            'denda_scf'       => ['nullable', 'integer', 'min:0'],
            'total_scf'       => ['nullable', 'integer', 'min:0'],

            'upah_act'        => ['nullable', 'integer', 'min:0'],
            'upah_bantu_act'  => ['nullable', 'integer', 'min:0'],
            'return_act'      => ['nullable', 'integer', 'min:0'],
            'denda_act'       => ['nullable', 'integer', 'min:0'],
            'total_act'       => ['nullable', 'integer', 'min:0'],

            'ket'             => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();
            if($validated['jam_masuk'] == 'Fleksibel'){ 
                $validated['jam_masuk'] = null;
            }

            if($validated['jam_pulang'] == 'Fleksibel'){
                $validated['jam_pulang'] = null;
            }

            $log->update([
                'tgl'             => $validated['tgl'],
                'shift'           => $validated['shift'],
                'employee_id'    => $validated['employee_id'],
                'aktifitas_id'    => $validated['aktifitas_id'],

                'jam_masuk'       => $validated['jam_masuk'] ?? null,
                'jam_pulang'      => $validated['jam_pulang'] ?? null,
                'jam_kerja_menit' => $validated['jam_kerja_menit'] ?? null,

                'hasil_kerja'     => $validated['hasil_kerja'] ?? 0,
                'hasil_lembur'    => $validated['hasil_lembur'] ?? 0,
                'return_qty'      => $validated['return_qty'] ?? 0,
                'tolak_qc'        => $validated['tolak_qc'] ?? 0,

                'upah_scf'        => $validated['upah_scf'] ?? 0,
                'bantu_scf'       => $validated['bantu_scf'] ?? 0,
                'denda_scf'       => $validated['denda_scf'] ?? 0,
                'total_scf'       => $validated['total_scf'] ?? 0,

                'upah_act'        => $validated['upah_act'] ?? 0,
                'upah_bantu_act'  => $validated['upah_bantu_act'] ?? 0,
                'return_act'      => $validated['return_act'] ?? 0,
                'denda_act'       => $validated['denda_act'] ?? 0,
                'total_act'       => $validated['total_act'] ?? 0,

                'ket'             => $validated['ket'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil diperbarui',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui log aktivitas',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    
    public function downloadAktifitas(Request $request){
        $status = $request->input('status');
        $search = $request->input('search');
        $filtered_jabatan = $request->input('filtered_jabatan');
        $filtered_perusahaan = $request->input('filtered_perusahaan');
        $filtered_tanggal_dari = $request->input('filtered_tanggal_dari');
        $filtered_tanggal_sampai = $request->input('filtered_tanggal_sampai');
        $employee_id = $request->input('employee_id');


        return Excel::download(
            new LogAktifitasExport(
                $search,
                $filtered_jabatan,
                $filtered_perusahaan,
                $filtered_tanggal_dari,
                $filtered_tanggal_sampai,
                $employee_id
            ),
            'log_aktifitas.xlsx'
        );
    }

    public function importAktifitas(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $log = null;

        try {
            $path = $request->file('file')->store('imports');

            if (!Storage::disk('local')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File upload failed',
                ], 500);
            }

            Log::info('File uploaded', [
                'path' => $path,
                'full_path' => Storage::disk('local')->path($path),
                'size' => $request->file('file')->getSize(),
            ]);

            $log = ImportLogAktifitas::create([
                'file'    => $path,
                'status'  => 'pending',
                'total'   => 0,
                'success' => 0,
                'failed'  => 0,
                'errors'  => null,
            ]);

            Log::info('Import log created', ['log_id' => $log->id]);

            Excel::queueImport(
                new AktifitasImport($log->id),
                $path,
                'local'
            );

            Log::info('Import queued successfully', [
                'log_id' => $log->id,
                'path' => $path,
            ]);

            return response()->json([
                'success'   => true,
                'import_id' => $log->id,
                'message'   => 'Import sedang diproses. Gunakan import_id untuk cek status.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Import queue failed', [
                'error' => $e->getMessage(),
            ]);

            if ($log) {
                // simpan error sebagai JSON array object
                $log->update([
                    'status' => 'failed',
                    'errors' => json_encode([[
                        'type' => 'queue_failed',
                        'error' => $e->getMessage(),
                    ]]),
                ]);
            }

            return response()->json([
                'success' => false,
                'error'   => 'Failed to queue import: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showImportLog($id)
    {
        try {
            $log = ImportLogAktifitas::findOrFail($id);

            return response()->json([
                'id'         => $log->id,
                'file'       => $log->file,
                'status'     => $log->status,
                'total'      => (int) $log->total,
                'success'    => (int) $log->success,
                'failed'     => (int) $log->failed,
                'errors'     => $log->errors ? json_decode($log->errors, true) : [],
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Import log not found',
            ], 404);
        }
    }

}
