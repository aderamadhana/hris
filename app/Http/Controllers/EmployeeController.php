<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Tampilkan halaman employee
     */
    public function index()
    {
        // Proteksi ekstra (walau idealnya pakai middleware)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        return Inertia::render('Dashboard', [
            'user' => $user,
        ]);
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
            'employments',
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
                'status'    => $employee->status_actiive,
                'nama'      => $employee->nama ?? null,
                'jk'        => $employee->jenis_kelamin ?? null,
                'agama'     => $employee->agama ?? null,
                'perkawinan'=> $employee->personal->status_perkawinan ?? null,
                'kewarganegaraan' => $employee->personal->kewarganegaraan ?? null,
                'tempat_lahir' => $employee->tempat_lahir ?? null,
                'tanggal_lahir'=> $employee->personal->tanggal_lahir ?? null,
                'usia' => optional($employee->personal->tanggal_lahir)
                            ? now()->diffInYears($employee->personal->tanggal_lahir)
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
            'pekerjaan' => $employee->employments->map(fn ($j) => [
                'jabatan'    => $j->job_role,
                'perusahaan' => $j->perusahaan,
                'bagian'     => $j->penempatan,
                'mulai'      => $j->tgl_awal_kerja,
                'selesai'    => $j->tgl_akhir_kerja,
                'jenis_kontrak' => $j->jenis_kontrak,
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
                'tinggi' => $employee->health->tb,
                'berat'  => $employee->health->bb,
                'tensi'  => $employee->health->tensi,
                'buta_warna' => $employee->health->buta_warna,
            ] : null,
        ]);
    }

}
