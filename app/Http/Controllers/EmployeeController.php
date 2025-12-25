<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\ImportLog;
use App\Models\ImportLogPayslip;
// use App\Models\EmployeePersonal;
// use App\Models\EmployeeAddress;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmployment;
use App\Models\EmployeeFamily;
use App\Models\EmployeeHealth;
use App\Models\EmployeeDocument;
use App\Models\User;
use App\Exports\EmployeesExport;

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
        $filtered_jabatan = trim($request->query('filtered_jabatan'));
        $filtered_perusahaan = trim($request->query('filtered_perusahaan'));
        $id_dummy = [1,2];

        $employees = Employee::with(['employments'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nrp', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('no_ktp', 'like', "%{$search}%")
                    ->orWhereHas('employments', function ($qe) use ($search) {
                        $qe->where('jabatan', 'like', "%{$search}%");
                    });
                });
            })
            // FILTER jabatan + perusahaan (di employee_employments)
            ->when($filtered_jabatan || $filtered_perusahaan, function ($query) use ($filtered_jabatan, $filtered_perusahaan) {
                $query->whereHas('employments', function ($qe) use ($filtered_jabatan, $filtered_perusahaan) {
                    $qe->when($filtered_jabatan, function ($q) use ($filtered_jabatan) {
                        $q->where('penempatan', $filtered_jabatan);
                    })
                    ->when($filtered_perusahaan, function ($q) use ($filtered_perusahaan) {
                        $q->where('perusahaan', $filtered_perusahaan);
                    });
                });
            })
            ->whereNotIn('id', $id_dummy)
            ->where('status_active', $status_active)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $data = $employees->getCollection()->map(function ($e) {
            return [
                'id' => $e->id,
                'name' => $e->nama,
                'nrp' => $e->nrp,
                'nik' => $e->no_ktp ?? '-',

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
                'user_id'   => $employee->user_id,
                'no_ktp'    => $employee->no_ktp,
                'no_kk'     => $employee->no_kk,
                'status_active' => $employee->status_active,
                'nama'      => $employee->nama,
                'jenis_kelamin' => $employee->jenis_kelamin,
                'agama'     => $employee->agama,
                'status_perkawinan' => $employee->status_perkawinan,
                'kewarganegaraan' => $employee->kewarganegaraan,
                'tempat_lahir' => $employee->tempat_lahir,
                'tanggal_lahir'=> $employee->tanggal_lahir
                    ? Carbon::parse($employee->tanggal_lahir)->format('d M Y')
                    : null,
                'usia' => $employee->tanggal_lahir
                    ? now()->diffInYears($employee->tanggal_lahir)
                    : null,

                // Kontak
                'no_wa' => $employee->no_wa,
                'email' => $employee->email,

                // BPJS
                'bpjs_tk' => $employee->bpjs_tk,
                'jenis_bpjs_tk' => $employee->jenis_bpjs_tk,
                'bpjs_kes' => $employee->bpjs_kes,
                'status_bpjs_ks' => $employee->status_bpjs_ks,
                'nama_faskes' => $employee->nama_faskes,

                // SKCK
                'no_skck' => $employee->no_skck,
                'masa_berlaku_skck' => $employee->masa_berlaku_skck
                    ? Carbon::parse($employee->masa_berlaku_skck)->format('d M Y')
                    : null,

                // Lisensi
                'jenis_lisensi' => $employee->jenis_lisensi,
                'no_lisensi' => $employee->no_lisensi,
                'masa_berlaku_lisensi' => $employee->masa_berlaku_lisensi
                    ? Carbon::parse($employee->masa_berlaku_lisensi)->format('d M Y')
                    : null,

                // Alamat
                'alamat_lengkap_ktp' => $employee->alamat_lengkap_ktp,
                'desa_ktp' => $employee->desa_ktp,
                'kecamatan_ktp' => $employee->kecamatan_ktp,
                'kota_ktp' => $employee->kota_ktp,
                'kode_pos_ktp' => $employee->kode_pos_ktp,
                'alamat_lengkap_domisili' => $employee->alamat_lengkap_domisili,
                'desa_domisili' => $employee->desa_domisili,
                'kecamatan_domisili' => $employee->kecamatan_domisili,
                'kota_domisili' => $employee->kota_domisili,
                'kode_pos_domisili' => $employee->kode_pos_domisili,

                // Bank
                'no_rekening' => $employee->no_rekening,
                'no_cif' => $employee->no_cif,
                'bank' => $employee->bank,

                // Pajak & atribut fisik
                'npwp' => $employee->npwp,
                'ptkp' => $employee->ptkp,
                'shoe_size' => $employee->shoe_size,
                'uniform_size' => $employee->uniform_size,

                // Payroll
                'gp' => $employee->gp,
                'via' => $employee->via,

                // Riwayat penggantian
                'reg_digantikan' => $employee->reg_digantikan,
                'nama_digantikan' => $employee->nama_digantikan,
            ],

            /* ===============================
            | TAB: PENDIDIKAN
            ===============================*/
            'pendidikan' => $employee->educations->map(fn ($p) => [
                'jenjang'     => $p->jenjang,
                'jurusan'     => $p->jurusan,
                'institusi'   => $p->institusi,
                'sekolah'     => $p->sekolah_asal,
                'tahun_lulus' => $p->tahun_lulus,
            ]),

            /* ===============================
            | TAB: RIWAYAT KERJA
            ===============================*/
            'pekerjaan' => $employee->employmentss->map(fn ($j) => [
                'perusahaan'      => $j->perusahaan,
                'jabatan'         => $j->jabatan ?? $j->job_roll,
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
                'tempat_lahir' => $f->tempat_lahir,
                'tanggal_lahir' => $f->tanggal_lahir,
                'ttl'       => ($f->tempat_lahir && $f->tanggal_lahir) 
                    ? $f->tempat_lahir . ', ' . Carbon::parse($f->tanggal_lahir)->format('d M Y')
                    : ($f->tempat_lahir ?? '-'),
                'pendidikan' => $f->pendidikan,
                'pekerjaan' => $f->pekerjaan,
                'tgl_perkawinan' => $f->tgl_perkawinan,
            ]),

            /* ===============================
            | DATA KESEHATAN
            ===============================*/
            'kesehatan' => $employee->health ? [
                'employee_id' => $employee->health->employee_id,
                'tanggal_mcu' => $employee->health->tanggal_mcu
                    ? Carbon::parse($employee->health->tanggal_mcu)->format('d M Y')
                    : null,
                'kesimpulan_hasil_mcu' => $employee->health->kesimpulan_hasil_mcu,
                'tinggi_badan' => $employee->health->tinggi_badan,
                'berat_badan'  => $employee->health->berat_badan,
                'gol_darah'    => $employee->health->gol_darah,
                'buta_warna'   => $employee->health->buta_warna,
                'hasil_drug_test' => $employee->health->hasil_drug_test,
                'tanggal_drug_test' => $employee->health->tanggal_drug_test
                    ? Carbon::parse($employee->health->tanggal_drug_test)->format('d M Y')
                    : null,
                'riwayat_penyakit' => $employee->health->riwayat_penyakit,
                'darah'  => $employee->health->darah,
                'urine'  => $employee->health->urine,
                'f_hati' => $employee->health->f_hati,
                'gula_darah' => $employee->health->gula_darah,
                'ginjal' => $employee->health->ginjal,
                'thorax' => $employee->health->thorax,
                'tensi'  => $employee->health->tensi,
                'nadi'   => $employee->health->nadi,
                'od'     => $employee->health->od,
                'os'     => $employee->health->os,
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
                'lisensi' => $employee->documents->dokumen_lisensi
                    ? url(Storage::url($employee->documents->dokumen_lisensi))
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
                'ijazah_terakhir' => $employee->documents->dokumen_ijazah_terakhir
                    ? url(Storage::url($employee->documents->dokumen_ijazah_terakhir))
                    : null,
                'skck' => $employee->documents->dokumen_skck
                    ? url(Storage::url($employee->documents->dokumen_skck))
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
            'no_ktp' => 'required|string|max:25|unique:employees,no_ktp',
        ]);

        DB::beginTransaction();

        try {
            // 1. Cek apakah perlu create user
            $userId = null;
            $shouldCreateUser = false;
            
            if ($request->pekerjaan) {
                $pekerjaan = json_decode($request->pekerjaan, true);
                
                // Cek apakah ada no_kontrak dengan status aktif
                foreach ($pekerjaan as $job) {
                    if (!empty($job['no_kontrak']) && 
                        !empty($job['status']) && 
                        strtolower($job['status']) === 'aktif') {
                        $shouldCreateUser = true;
                        break;
                    }
                }
            }

            // Create user jika memenuhi syarat
            if ($shouldCreateUser && $request->email) {
                $existingUser = User::where('email', $request->email)->first();
                
                if (!$existingUser) {
                    $user = User::create([
                        'name' => $request->nama,
                        'email' => $request->email,
                        'password' => Hash::make($request->nrp),
                        'role_id' => '2',
                        'email_verified_at' => now(),
                    ]);
                    $userId = $user->id;
                } else {
                    $userId = $existingUser->id;
                }
            }

            // 2. Create Employee (semua data langsung di table employees)
            $employee = Employee::create([
                // Data utama
                'nrp' => $request->nrp,
                'user_id' => $userId,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan' => $request->kewarganegaraan,
                'status_active' => $request->status_active ?? '1',
                
                // Data personal (dulunya di employee_personals)
                'no_ktp' => $request->no_ktp,
                'no_wa' => $request->no_wa,
                'no_kk' => $request->kk,
                'bpjs_tk' => $request->bpjs_tk,
                'bpjs_kes' => $request->bpjs_kes,
                'jenis_bpjs_tk' => $request->jenis_bpjs_tk,
                'nama_faskes' => $request->nama_faskes,
                'status_bpjs_ks' => $request->status_bpjs_ks,
                'email' => $request->email,
                'no_skck' => $request->no_skck,
                'masa_berlaku_skck' => $request->masa_berlaku_skck,
                'jenis_lisensi' => $request->jenis_lisensi,
                'no_lisensi' => $request->no_lisensi,
                'masa_berlaku_lisensi' => $request->masa_berlaku_lisensi,
                
                // Data alamat domisili (dulunya di employee_addresses)
                'alamat_lengkap_domisili' => $request->alamat_lengkap_domisili,
                'kota_domisili' => $request->kota_domisili,
            ]);

            // 3. Create Employee Education Records
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

            // 4. Create Employee Employment History
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

            // 5. Create Employee Family Members
            if ($request->keluarga) {
                $keluargaList = json_decode($request->keluarga, true);
                
                foreach ($keluargaList as $fam) {
                    if (!empty($fam['nama'])) {
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

            // 6. Create Employee Health Record
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
                'tanggal_mcu' => $request->tanggal_mcu,
                'kesimpulan_hasil_mcu' => $request->kesimpulan_hasil_mcu,
            ]);

            // 7. Handle Document Uploads
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

            // 1. Cek apakah perlu update user
            $userId = $employee->user_id;
            $shouldCreateUser = false;
            
            if ($request->pekerjaan) {
                $pekerjaan = json_decode($request->pekerjaan, true);
                
                // Cek apakah ada no_kontrak dengan status aktif
                foreach ($pekerjaan as $job) {
                    if (!empty($job['no_kontrak']) && 
                        !empty($job['status']) && 
                        strtolower($job['status']) === 'aktif') {
                        $shouldCreateUser = true;
                        break;
                    }
                }
            }

            // Create/update user jika memenuhi syarat
            if ($shouldCreateUser && $request->email) {
                $existingUser = User::where('email', $request->email)->first();
                
                if (!$existingUser && !$userId) {
                    // Create new user
                    $user = User::create([
                        'name' => $request->nama,
                        'email' => $request->email,
                        'password' => Hash::make($request->nrp),
                        'role_id' => '2',
                        'email_verified_at' => now(),
                    ]);
                    $userId = $user->id;
                } elseif ($existingUser) {
                    $userId = $existingUser->id;
                }
            }

            // 2. Update Employee (semua data langsung di table employees)
            $employee->update([
                // Data utama
                'nrp' => $request->nrp,
                'user_id' => $userId,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'kewarganegaraan' => $request->kewarganegaraan,
                'status_active' => $request->status_active ?? '1',
                
                // Data personal (dulunya di employee_personals)
                'no_ktp' => $request->no_ktp,
                'no_wa' => $request->no_wa,
                'no_kk' => $request->kk,
                'bpjs_tk' => $request->bpjs_tk,
                'bpjs_kes' => $request->bpjs_kes,
                'jenis_bpjs_tk' => $request->jenis_bpjs_tk,
                'nama_faskes' => $request->nama_faskes,
                'status_bpjs_ks' => $request->status_bpjs_ks,
                'email' => $request->email,
                'no_skck' => $request->no_skck,
                'masa_berlaku_skck' => $request->masa_berlaku_skck,
                'jenis_lisensi' => $request->jenis_lisensi,
                'no_lisensi' => $request->no_lisensi,
                'masa_berlaku_lisensi' => $request->masa_berlaku_lisensi,
                
                // Data alamat domisili (dulunya di employee_addresses)
                'alamat_lengkap_domisili' => $request->alamat_lengkap_domisili,
                'kota_domisili' => $request->kota_domisili,
            ]);

            // 3. Update Employee Education Records
            $employee->educations()->delete();
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

            // 4. Update Employee Employment History
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

            // 5. Update Employee Family Members
            EmployeeFamily::where('employee_id', $employee->id)->delete();
            if ($request->keluarga) {
                $keluargaList = json_decode($request->keluarga, true);
                
                foreach ($keluargaList as $fam) {
                    if (!empty($fam['nama'])) {
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

            // 6. Update Employee Health Record
            $employee->health()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
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
                    'tanggal_mcu' => $request->tanggal_mcu,
                    'kesimpulan_hasil_mcu' => $request->kesimpulan_hasil_mcu,
                ]
            );

            // 7. Handle Document Uploads
            $this->handleDocumentUploads($request, $employee->id);

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
            'dokumen_ijazah_terakhir'        => 'dokumen_ijazah_terakhir',
            'dokumen_skck'       => 'dokumen_skck',
            'dokumen_paklaring'     => 'dokumen_surat_pengalaman_kerja',
            'dokumen_ktp'           => 'dokumen_ktp',
            'dokumen_form_bpjs_tk'  => 'dokumen_bpjs_ketenagakerjaan',
            'dokumen_form_bpjs_kes' => 'dokumen_formulir_bpjs_kesehatan',
            'dokumen_lisensi'        => 'dokumen_lisensi',
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

    public function downloadEmployees(Request $request){
        $search = $request->input('search');
        $statusActive = (int) $request->input('status_active', 1);
        $filteredJabatan = $request->input('filtered_jabatan');
        $filteredPerusahaan = $request->input('filtered_perusahaan');

        return Excel::download(
            new EmployeesExport($search, $statusActive, $filteredJabatan, $filteredPerusahaan),
            'karyawan.xlsx'
        );
    }

}
