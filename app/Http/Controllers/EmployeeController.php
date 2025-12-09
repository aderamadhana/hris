<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\ImportLog;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Jobs\ImportEmployeesJob;
use Illuminate\Http\Request;
use App\Imports\EmployeesImport;

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
                'tanggal_lahir'=> $employee->tanggal_lahir ?? null,
                'usia' => optional($employee->tanggal_lahir)
                            ? now()->diffInYears($employee->tanggal_lahir)
                            : null,
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
                'tinggi_badan'      => $employee->health->tinggi_badan,
                'berat_badan'       => $employee->health->berat_badan,
                'gol_darah'         => $employee->health->gol_darah,
                'buta_warna'        => $employee->health->buta_warna,
                'riwayat_penyakit'  => $employee->health->riwayat_penyakit,
                'tanggal_mcu'       => $employee->health->tanggal_mcu,
            ] : null,
        ]);
    }

    public function import(Request $request)
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
