<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\ImportLog;
use App\Models\ImportLogPayslip;
use App\Models\EmployeePersonal;
use App\Models\EmployeeAddress;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmployment;
use App\Models\EmployeeFamily;
use App\Models\EmployeeHealth;
use App\Models\EmployeeDocument;
use App\Models\User;

use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\EmployeesImport;
use App\Imports\PayslipImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Tampilkan halaman employee
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 1), 100);

        $search = trim($request->query('search'));
        $status_active = trim($request->query('status_active'));

        $employees = Employee::with(['personal', 'employments'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nrp', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhereHas('personal', function ($qe) use ($search) {
                        $qe->where('no_ktp', 'like', "%{$search}%");
                    })
                    ->orWhereHas('employments', function ($qe) use ($search) {
                        $qe->where('jabatan', 'like', "%{$search}%");
                    });
                });
            })
            ->where('status_active', $status_active)
            ->orderBy('nama')
            ->paginate($perPage);

        $data = $employees->getCollection()->map(function ($e) {
            return [
                'id' => $e->id,
                'name' => $e->nama,
                'nrp' => $e->nrp,
                'nik' => $e->personal->no_ktp ?? '-',

                'tanggal_lahir' => $e->tanggal_lahir
                    ? \Carbon\Carbon::parse($e->tanggal_lahir)->format('d/m/Y')
                    : '-',

                'perusahaan' => optional($e->employments)->perusahaan ?? '-',
                'department' => optional($e->employments)->jabatan ?? '-',
                'position' => optional($e->employments)->penempatan ?? '-',

                'awal_kontrak' => optional($e->employments)->tgl_awal_kerja
                    ? \Carbon\Carbon::parse($e->employments->tgl_awal_kerja)->format('d/m/Y')
                    : '-',

                'akhir_kontrak' => optional($e->employments)->tgl_akhir_kerja
                    ? \Carbon\Carbon::parse($e->employments->tgl_akhir_kerja)->format('d/m/Y')
                    : '-',
                'status' => $e->status_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
            ]
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
    
    public function detailPelamar($id)
    {
        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        return Inertia::render('employee/DetailPelamar', [
            'user' => $user,
        ]);
    }

    public function changePassword()
    {
        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        return Inertia::render('settings/GantiPassword', [
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
            'health',
            'documents'
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

            /* ===============================
            | TAB: DOKUMEN
            ===============================*/
            'document' => $employee->documents ? [
            'pas_foto' => $employee->documents->pas_foto
                ? url(Storage::url($employee->documents->pas_foto))
                : null,

            'ktp' => $employee->documents->dokumen_ktp
                ? url(Storage::url($employee->documents->dokumen_ktp))
                : null,

            'kk' => $employee->documents->dokumen_kk
                ? url(Storage::url($employee->documents->dokumen_kk))
                : null,

            'bpjs_tk' => $employee->documents->dokumen_bpjs_ketenagakerjaan
                ? url(Storage::url($employee->documents->dokumen_bpjs_ketenagakerjaan))
                : null,

            'vaksin' => $employee->documents->dokumen_sertifikat_vaksin
                ? url(Storage::url($employee->documents->dokumen_sertifikat_vaksin))
                : null,

            'sio_forklift' => $employee->documents->dokumen_sio_forklift
                ? url(Storage::url($employee->documents->dokumen_sio_forklift))
                : null,

            'form_bpjs_tk' => $employee->documents->dokumen_formulir_bpjs_tk
                ? url(Storage::url($employee->documents->dokumen_formulir_bpjs_tk))
                : null,

            'form_bpjs_kes' => $employee->documents->dokumen_formulir_bpjs_kesehatan
                ? url(Storage::url($employee->documents->dokumen_formulir_bpjs_kesehatan))
                : null,

            'paklaring' => $employee->documents->dokumen_surat_pengalaman_kerja
                ? url(Storage::url($employee->documents->dokumen_surat_pengalaman_kerja))
                : null,

            'sim_b1' => $employee->documents->dokumen_sim_b1
                ? url(Storage::url($employee->documents->dokumen_sim_b1))
                : null,

            'kartu_garda' => $employee->documents->dokumen_kartu_garda_pratama
                ? url(Storage::url($employee->documents->dokumen_kartu_garda_pratama))
                : null,
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

    public function prosesChangePassword(Request $request)
    {
        // VALIDASI KERAS
        $request->validate([
            'password' => 'required|min:5|confirmed',
        ]);

        $user = Auth::user();

        // return $user;

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // UPDATE PASSWORD
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data utama
        $request->validate([
            'nama' => 'required|string|max:150',
            'nrp' => 'required|string|max:30|unique:employees,nrp',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'email' => 'nullable|email|max:100',
            'no_ktp' => 'required|string|max:25',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create Employee (Main Table)
            $employee = Employee::create([
                'nrp' => $request->nrp,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan' => $request->kewarganegaraan,
                'status_active' => $request->status_active ?? '1',
            ]);

            // 2. Create User if no_kontrak is provided AND status kontrak is 'aktif'
            $userId = null;
            if ($request->pekerjaan) {
                $pekerjaan = json_decode($request->pekerjaan, true);
                
                // Cek apakah ada no_kontrak dengan status aktif
                $shouldCreateUser = false;
                foreach ($pekerjaan as $job) {
                    if (!empty($job['no_kontrak']) && 
                        !empty($job['status']) && 
                        strtolower($job['status']) === 'aktif') {
                        $shouldCreateUser = true;
                        break;
                    }
                }

                // Create user jika memenuhi syarat: ada no_kontrak, status aktif, dan email tersedia
                if ($shouldCreateUser && $request->email) {
                    // Cek apakah email sudah terdaftar
                    $existingUser = User::where('email', $request->email)->first();
                    
                    if (!$existingUser) {
                        $user = User::create([
                            'name' => $request->nama,
                            'email' => $request->email,
                            'password' => Hash::make($request->nrp), // Default password = NRP
                            'role_id' => '2', // Default role
                            'email_verified_at' => now(),
                        ]);
                        
                        $userId = $user->id;
                        
                        // Update employee dengan user_id
                        $employee->update(['user_id' => $userId]);
                    } else {
                        // Jika user sudah ada, link ke employee
                        $userId = $existingUser->id;
                        $employee->update(['user_id' => $userId]);
                    }
                }
            }

            // 3. Create Employee Personal Data
            EmployeePersonal::create([
                'employee_id' => $employee->id,
                'no_ktp' => $request->no_ktp,
                'no_wa' => $request->no_wa,
                'bpjs_tk' => $request->bpjs_tk,
                'bpjs_kes' => $request->bpjs_kes,
                'nama_faskes' => $request->nama_faskes,
                'email' => $request->email,
                'no_skck' => $request->no_skck,
                'masa_berlaku_skck' => $request->masa_berlaku_skck,
                'jenis_lisensi' => $request->jenis_lisensi,
                'no_lisensi' => $request->no_lisensi,
                'masa_berlaku_lisensi' => $request->masa_berlaku_lisensi,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan' => $request->kewarganegaraan,
            ]);

            // 4. Create Employee Address
            if ($request->alamat_lengkap) {
                EmployeeAddress::create([
                    'employee_id' => $employee->id,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'kota' => $request->kota,
                    'tipe' => $request->tipe ?? 'Domisili',
                ]);
            }

            // 5. Create Employee Education Records
            if ($request->pendidikan) {
                $pendidikanList = json_decode($request->pendidikan, true);
                
                foreach ($pendidikanList as $edu) {
                    if (!empty($edu['jenjang']) || !empty($edu['institusi'])) {
                        EmployeeEducation::create([
                            'employee_id' => $employee->id,
                            'jenjang' => $edu['jenjang'] ?? null,
                            'jurusan' => $edu['jurusan'] ?? null,
                            'institusi' => $edu['institusi'] ?? null,
                            'tahun_lulus' => $edu['tahun_lulus'] ?? null,
                        ]);
                    }
                }
            }

            // 6. Create Employee Employment History
            if ($request->pekerjaan) {
                $pekerjaanList = json_decode($request->pekerjaan, true);
                
                foreach ($pekerjaanList as $job) {
                    if (!empty($job['perusahaan']) || !empty($job['jabatan'])) {
                        EmployeeEmployment::create([
                            'employee_id' => $employee->id,
                            'perusahaan' => $job['perusahaan'] ?? null,
                            'jabatan' => $job['jabatan'] ?? null,
                            'penempatan' => $job['penempatan'] ?? null,
                            'no_kontrak' => $job['no_kontrak'] ?? null,
                            'cost_center' => $job['cost_center'] ?? null,
                            'jenis_kontrak' => $job['jenis_kontrak'] ?? null,
                            'tgl_awal_kerja' => $job['tgl_awal_kerja'] ?? null,
                            'tgl_akhir_kerja' => $job['tgl_akhir_kerja'] ?? null,
                            'jenis_kerja' => $job['jenis_kerja'] ?? null,
                            'pola_kerja' => $job['pola_kerja'] ?? null,
                            'hari_kerja' => $job['hari_kerja'] ?? null,
                            'status' => $job['status'] ?? null,
                        ]);
                    }
                }
            }

            // 7. Create Employee Family Members
            if ($request->keluarga) {
                $keluargaList = json_decode($request->keluarga, true);
                
                foreach ($keluargaList as $fam) {
                    if (!empty($fam['nama'])) {
                        // Parse tanggal lahir dari format "01-01-2000" atau "2000-01-01"
                        $tanggalLahir = null;
                        if (!empty($fam['tanggal_lahir'])) {
                            $tanggalLahir = $this->parseDateString($fam['tanggal_lahir']);
                        }

                        EmployeeFamily::create([
                            'employee_id' => $employee->id,
                            'nama' => $fam['nama'],
                            'hubungan' => $fam['hubungan'] ?? null,
                            'tempat_lahir' => $fam['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $tanggalLahir,
                        ]);
                    }
                }
            }

            // 8. Create Employee Health Record
            EmployeeHealth::create([
                'employee_id' => $employee->id,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
                'gol_darah' => $request->gol_darah,
                'buta_warna' => $request->buta_warna == '1',
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'hasil_drug_test' => $request->hasil_drug_test,
                'tanggal_drug_test' => $request->tanggal_drug_test,
                'darah' => $request->darah,
                'urine' => $request->urine,
                'f_hati' => $request->f_hati,
                'gula_darah' => $request->gula_darah,
                'ginjal' => $request->ginjal,
                'thorax' => $request->thorax,
                'tensi' => $request->tensi,
                'nadi' => $request->nadi,
                'od' => $request->od,
                'os' => $request->os,
            ]);

            // 9. Handle Document Uploads
            $this->handleDocumentUploads($request, $employee->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil disimpan',
                'employee_id' => $employee->id,
                'user_created' => $userId !== null,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified employee with all relations
     */

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        return Inertia::render('admin/EditKaryawan', [
            'employee_id' => $employee->id
        ]);

        return response()->json($employee);
    }

    public function show($id)
    {
        $employee = Employee::with([
            'personal',
            'address',
            'educations',
            'employmentss',
            'families',
            'health',
            'user',
            'documents',
        ])->findOrFail($id);

        return response()->json($employee);
    }
    /**
     * Update the specified employee
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nrp' => 'required|string|max:30|unique:employees,nrp,' . $id,
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        DB::beginTransaction();

        try {
            $employee = Employee::findOrFail($id);

            // Update employee
            $employee->update([
                'nrp' => $request->nrp,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan' => $request->kewarganegaraan,
                'status_active' => $request->status_active ?? '1',
            ]);

            // Update personal data
            $employee->personal()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'no_ktp' => $request->no_ktp,
                    'no_wa' => $request->no_wa,
                    'bpjs_tk' => $request->bpjs_tk,
                    'bpjs_kes' => $request->bpjs_kes,
                    'nama_faskes' => $request->nama_faskes,
                    'email' => $request->email,
                    'no_skck' => $request->no_skck,
                    'masa_berlaku_skck' => $request->masa_berlaku_skck,
                    'jenis_lisensi' => $request->jenis_lisensi,
                    'no_lisensi' => $request->no_lisensi,
                    'masa_berlaku_lisensi' => $request->masa_berlaku_lisensi,
                ]
            );

            // Update health record
            $employee->health()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'tinggi_badan' => $request->tinggi_badan,
                    'berat_badan' => $request->berat_badan,
                    'gol_darah' => $request->gol_darah,
                    'buta_warna' => $request->buta_warna == '1',
                    'riwayat_penyakit' => $request->riwayat_penyakit,
                    // ... other health fields
                ]
            );

            // Update educations - delete old and create new
            if ($request->pendidikan) {
                $employee->educations()->delete();
                $pendidikanList = json_decode($request->pendidikan, true);
                
                foreach ($pendidikanList as $edu) {
                    if (!empty($edu['jenjang']) || !empty($edu['institusi'])) {
                        $employee->educations()->create($edu);
                    }
                }
            }

            // 6. Create Employee Employment History
            EmployeeEmployment::where('employee_id', $employee->id)->delete();
            if ($request->pekerjaan) {
                $pekerjaanList = json_decode($request->pekerjaan, true);
                
                foreach ($pekerjaanList as $job) {
                    if (!empty($job['perusahaan']) || !empty($job['jabatan'])) {
                        EmployeeEmployment::create([
                            'employee_id' => $employee->id,
                            'perusahaan' => $job['perusahaan'] ?? null,
                            'jabatan' => $job['jabatan'] ?? null,
                            'penempatan' => $job['penempatan'] ?? null,
                            'no_kontrak' => $job['no_kontrak'] ?? null,
                            'cost_center' => $job['cost_center'] ?? null,
                            'jenis_kontrak' => $job['jenis_kontrak'] ?? null,
                            'tgl_awal_kerja' => $job['tgl_awal_kerja'] ?? null,
                            'tgl_akhir_kerja' => $job['tgl_akhir_kerja'] ?? null,
                            'jenis_kerja' => $job['jenis_kerja'] ?? null,
                            'pola_kerja' => $job['pola_kerja'] ?? null,
                            'hari_kerja' => $job['hari_kerja'] ?? null,
                            'status' => $job['status'] ?? null,
                        ]);
                    }
                }
            }

            // 7. Create Employee Family Members
            EmployeeFamily::where('employee_id', $employee->id)->delete();
            if ($request->keluarga) {
                $keluargaList = json_decode($request->keluarga, true);
                
                foreach ($keluargaList as $fam) {
                    if (!empty($fam['nama'])) {
                        // Parse tanggal lahir dari format "01-01-2000" atau "2000-01-01"
                        $tanggalLahir = null;
                        if (!empty($fam['tanggal_lahir'])) {
                            $tanggalLahir = $this->parseDateString($fam['tanggal_lahir']);
                        }

                        EmployeeFamily::create([
                            'employee_id' => $employee->id,
                            'nama' => $fam['nama'],
                            'hubungan' => $fam['hubungan'] ?? null,
                            'tempat_lahir' => $fam['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $tanggalLahir,
                        ]);
                    }
                }
            }
            
            $this->handleDocumentUploads($request, $employee->id);
            // Similar logic for pekerjaan and keluarga...

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui',
                'employee' => $employee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            // Soft delete atau hard delete
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle document file uploads
     */
    private function handleDocumentUploads(Request $request, int $employeeId): void
    {
        $map = [
            'dokumen_pas_foto'      => 'pas_foto',
            'dokumen_kk'            => 'dokumen_kk',
            'dokumen_vaksin'        => 'dokumen_sertifikat_vaksin',
            'dokumen_bpjs_tk'       => 'dokumen_formulir_bpjs_tk',
            'dokumen_paklaring'     => 'dokumen_surat_pengalaman_kerja',
            'dokumen_kartu_garda'   => 'dokumen_kartu_garda_pratama',
            'dokumen_ktp'           => 'dokumen_ktp',
            'dokumen_form_bpjs_tk'  => 'dokumen_bpjs_ketenagakerjaan',
            'dokumen_sio_forklift'  => 'dokumen_sio_forklift',
            'dokumen_form_bpjs_kes' => 'dokumen_formulir_bpjs_kesehatan',
            'dokumen_sim_b1'        => 'dokumen_sim_b1',
        ];

        // pastikan ada row
        $doc = EmployeeDocument::firstOrCreate([
            'employee_id' => $employeeId,
        ]);

        // field yang BENAR-BENAR dikirim client
        $sentFields = array_unique(array_merge(
            array_keys($request->request->all()), // boolean / string
            array_keys($request->files->all())    // file
        ));

        // 1️⃣ HAPUS SEMUA DOKUMEN YANG TIDAK DIKIRIM
        foreach ($map as $fieldName => $columnName) {

            if (!in_array($fieldName, $sentFields, true)) {
                $oldPath = $doc->{$columnName};

                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $doc->{$columnName} = null;
            }
        }

        // 2️⃣ VALIDASI FILE (hanya yang dikirim)
        $rules = [];
        foreach ($map as $fieldName => $columnName) {
            if ($request->hasFile($fieldName)) {
                $rules[$fieldName] = 'file|mimes:pdf,jpg,jpeg,png|max:5120';
            }
        }
        if ($rules) {
            $request->validate($rules);
        }

        // 3️⃣ UPLOAD / REPLACE
        foreach ($map as $fieldName => $columnName) {

            // keep (true) → skip
            if (in_array($request->input($fieldName), [true, 'true'], true)) {
                continue;
            }

            if (!$request->hasFile($fieldName)) {
                continue;
            }

            $file = $request->file($fieldName);

            $oldPath = $doc->{$columnName};
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $filename =
                "employee_{$employeeId}_{$columnName}_" .
                now()->format('YmdHis') . "_" .
                Str::random(8) . "." .
                $file->getClientOriginalExtension();

            $doc->{$columnName} = $file->storeAs(
                "documents/employees/{$employeeId}",
                $filename,
                'public'
            );
        }

        if ($doc->isDirty()) {
            $doc->save();
        }
    }

    /**
     * Parse date string to proper date format
     */
    private function parseDateString($dateString)
    {
        try {
            // Try format: DD-MM-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateString)) {
                $parts = explode('-', $dateString);
                return "{$parts[2]}-{$parts[1]}-{$parts[0]}"; // Convert to YYYY-MM-DD
            }
            
            // If already in YYYY-MM-DD format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
                return $dateString;
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

}
