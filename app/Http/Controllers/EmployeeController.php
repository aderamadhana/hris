<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\ImportLog;
use App\Models\ImportLogPayslip;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\EmployeesImport;
use App\Imports\PayslipImport;

class EmployeeController extends Controller
{
    /**
     * Tampilkan halaman employee
     */
    public function index(Request $request)
    {
        $employees = Employee::with('personal','employments')
            ->orderBy('nama')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'name' => $e->nama,
                    'nrp' => $e->nrp,
                    'nik' => $e->personal->no_ktp ?? '-',
                    'tanggal_lahir' => $e->tanggal_lahir
                    ? \Carbon\Carbon::parse($e->tanggal_lahir)->format('d/m/Y')
                    : '-',
                    'perusahaan' =>$e->employments->perusahaan ?? '-',
                    'department' => $e->employments->jabatan ?? '-',
                    'position' =>$e->employments->penempatan ?? '-',
                    'status' => $e->status_active ? 'Aktif' : 'Nonaktif',
                ];
            });

        return response()->json($employees);
    }

    public function profil($id)
    {
        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        return Inertia::render('employee/Profil', [
            'user' => $user,
        ]);
    }

    public function getData($id){
        $employee = Employee::with([
            'personal',
            'address',
            'educations',
            'employmentss',
            'families',
            'health'
        ])->findOrFail($id);

        return response()->json([
            /* ===============================
             | TAB: DATA KARYAWAN
             ===============================*/
            'employee' => [
                'id'        => $employee->id,
                'nrp'       => $employee->nrp,
                'nik'       => $employee->personal->nik,
                'status'    => $employee->status_active,
                'nama'      => $employee->nama ?? null,
                'jk'        => $employee->jenis_kelamin ?? null,
                'agama'     => $employee->agama ?? null,
                'perkawinan'=> $employee->status_perkawinan ?? null,
                'kewarganegaraan' => $employee->kewarganegaraan ?? null,
                'tempat_lahir' => $employee->tempat_lahir ?? null,
                'tanggal_lahir'=> Carbon::parse($employee->tanggal_lahir)->format('d M Y') ?? null,
                'usia' => optional($employee->tanggal_lahir)
                            ? now()->diffInYears($employee->tanggal_lahir)
                            : null,

                // Kontak
                'no_wa' => optional($employee->personal)->no_wa,
                'email' => optional($employee->personal)->email,

                // BPJS
                'bpjs_tk' => optional($employee->personal)->bpjs_tk,
                'x' => optional($employee->personal)->x,
                'bpjs_kes' => optional($employee->personal)->bpjs_kes,
                'x_ks' => optional($employee->personal)->x_ks,
                'nama_faskes' => optional($employee->personal)->nama_faskes,

                // SKCK
                'no_skck' => optional($employee->personal)->no_skck,
                'masa_berlaku_skck' => optional($employee->personal?->masa_berlaku_skck)
                    ? Carbon::parse($employee->personal->masa_berlaku_skck)->format('d M Y')
                    : null,

                // Lisensi
                'jenis_lisensi' => optional($employee->personal)->jenis_lisensi,
                'no_lisensi' => optional($employee->personal)->no_lisensi,
                'masa_berlaku_lisensi' => optional($employee->personal?->masa_berlaku_lisensi)
                    ? Carbon::parse($employee->personal->masa_berlaku_lisensi)->format('d M Y')
                    : null,

                // Bank
                'no_rekening' => optional($employee->personal)->no_rekening,
                'no_cif' => optional($employee->personal)->no_cif,
                'bank' => optional($employee->personal)->bank,

                // Pajak & atribut fisik
                'npwp' => optional($employee->personal)->npwp,
                'ptkp' => optional($employee->personal)->ptkp,
                'shoe_size' => optional($employee->personal)->shoe_size,
                'uniform_size' => optional($employee->personal)->uniform_size,

                // Payroll (tetap di employee)
                'gp' => $employee->gp,
                'via' => $employee->via,

                // Riwayat penggantian
                'reg_digantikan' => optional($employee->personal)->reg_digantikan,
                'nama_digantikan' => optional($employee->personal)->nama_digantikan,

            ],

            /* ===============================
             | KONTAK & ALAMAT
             ===============================*/
            'alamat' => [
                'ktp'       => $employee->personal->no_ktp ?? null,
                'email'     => $employee->personal->email ?? null,
                'phone'     => $employee->personal->no_hp ?? null,
                'domisili'  => $employee->address->alamat_lengkap ?? null,
                'tinggal'   => $employee->address->alamat_tinggal ?? null,
                'kota'      => $employee->address->kota ?? null,
            ],

            /* ===============================
             | TAB: PENDIDIKAN
             ===============================*/
            'pendidikan' => $employee->educations->map(fn ($p) => [
                'jenjang'     => $p->jenjang,
                'jurusan'     => $p->jurusan,
                'sekolah'     => $p->sekolah_asal,
                'tahun_lulus' => $p->tahun_lulus,
            ]),

            /* ===============================
             | TAB: RIWAYAT KERJA
             ===============================*/
            'pekerjaan' => $employee->employmentss->map(fn ($j) => [
                'perusahaan'      => $j->perusahaan,
                'jabatan'         => $j->jabatan ?? $j->job_roll, // pilih salah satu sesuai model
                'bagian'          => $j->penempatan,
                'mulai'           => $j->tgl_awal_kerja,
                'selesai'         => $j->tgl_akhir_kerja,
                'jenis_kontrak'   => $j->jenis_kontrak,
                'no_kontrak'      => $j->no_kontrak,
                'cost_center'     => $j->cost_center,
                'tgl_daftar'      => $j->tgl_daftar,
                'status_kontrak'  => $j->keterangan_status ?? $j->status,
                'masa_kerja'      => $j->masa_kerja,
                'pola_kerja'      => $j->pola_kerja,
                'jenis_kerja'     => $j->jenis_kerja,
                'hari_kerja'      => $j->hari_kerja,
            ]),

            /* ===============================
             | TAB: KELUARGA
             ===============================*/
            'keluarga' => $employee->families->map(fn ($f) => [
                'nama'      => $f->nama,
                'hubungan'  => $f->hubungan,
                'ttl'       => $f->tempat_lahir . ', ' . $f->tanggal_lahir,
                'no_hp'     => $f->no_hp,
            ]),

            /* ===============================
             | DATA KESEHATAN (opsional tab)
             ===============================*/
            'kesehatan' => $employee->health ? [
                'employee_id'        => $employee->health->employee_id,

                'tanggal_mcu'        => optional($employee->health->tanggal_mcu)
                                            ? Carbon::parse($employee->health->tanggal_mcu)->format('d M Y')
                                            : null,

                'tinggi_badan'       => $employee->health->tinggi_badan,
                'berat_badan'        => $employee->health->berat_badan,
                'gol_darah'          => $employee->health->gol_darah,
                'buta_warna'         => $employee->health->buta_warna,

                'hasil_drug_test'    => $employee->health->hasil_drug_test,
                'tanggal_drug_test'  => optional($employee->health->tanggal_drug_test)
                                            ? Carbon::parse($employee->health->tanggal_drug_test)->format('d M Y')
                                            : null,

                'riwayat_penyakit'   => $employee->health->riwayat_penyakit,

                // Detail MCU tambahan
                'darah'              => $employee->health->darah,
                'urine'              => $employee->health->urine,
                'f_hati'             => $employee->health->f_hati,
                'gula_darah'         => $employee->health->gula_darah,
                'ginjal'             => $employee->health->ginjal,
                'thorax'             => $employee->health->thorax,
                'tensi'              => $employee->health->tensi,
                'nadi'               => $employee->health->nadi,
                'od'                 => $employee->health->od,
                'os'                 => $employee->health->os,
            ] : null,

        ]);
    }

    public function importKaryawan(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        // simpan ke storage
        $path = $request->file('file')->store('imports');

        // BUAT LOG dasar, tanpa expected_total
        $log = ImportLog::create([
            'file'   => $path,
            'status' => 'processing',
            'total'  => 0,
            'success'=> 0,
            'failed' => 0,
            'errors' => null,
        ]);

        // QUEUE IMPORT – path + disk saja (aman di-serialize)
        Excel::queueImport(
            new EmployeesImport($log->id),
            $path,
            'local',
            ExcelExcel::XLSX
        );

        return response()->json([
            'import_id' => $log->id,
        ]);
    }

    public function importPayslip(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'payroll_period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            $import = new PayslipImport($request->payroll_period_id);
            
            Excel::queueImport($import, $request->file('file'));
            
            $results = $import->getResults();
            
            if ($results['failed'] > 0) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Import completed with errors",
                    'data' => $results
                ], 207);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => "Successfully imported {$results['success']} records",
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showImportLog($id)
    {
        $log = ImportLog::findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'status' => $log->status,
            'total' => $log->total,
            'success' => $log->success,
            'failed' => $log->failed,
            'errors' => json_decode($log->errors, true),
            'updated_at' => $log->updated_at,
        ]);
    }

}
