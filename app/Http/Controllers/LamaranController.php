<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHealthRecord;
use App\Models\EmployeeFamilyMember;
use App\Models\EmployeeEducation;
use App\Models\EmployeeDocument;
use App\Models\EmployeeEmployment;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LamaranController extends Controller
{
    public function store(Request $request, $lokerId)
    {
        // Decode JSON strings SEBELUM validasi
        $employee = json_decode($request->input('employee'), true) ?? [];
        $alamat = json_decode($request->input('alamat'), true) ?? [];
        $pendidikan = json_decode($request->input('pendidikan'), true) ?? [];
        $keluarga = json_decode($request->input('keluarga'), true) ?? [];
        $kesehatan = json_decode($request->input('kesehatan'), true) ?? [];

        // Validasi manual dari decoded data
        $validator = \Validator::make($employee, [
            'nama' => 'required|string|max:150',
            'jk' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi file
        $request->validate([
            'pas_foto' => 'nullable|file|image|max:2048',
            'dokumen_kk' => 'nullable|file|max:2048',
            'dokumen_surat_pengalaman_kerja' => 'nullable|file|max:2048',
            'dokumen_ktp' => 'nullable|file|max:2048',
            'dokumen_bpjs_ketenagakerjaan' => 'nullable|file|max:2048',
            'dokumen_sio_forklift' => 'nullable|file|max:2048',
            'dokumen_formulir_bpjs_kesehatan' => 'nullable|file|max:2048',
            'dokumen_ijazah_terakhir' => 'nullable|file|max:2048',
            'dokumen_skck' => 'nullable|file|max:2048',
            'dokumen_lisensi' => 'nullable|file|max:2048',
        ]);

        // Ambil data loker
        $loker = Loker::findOrFail($lokerId);

        DB::beginTransaction();
        
        try {
            // Simpan Employee
            $employeeData = Employee::create([
                'nama' => $employee['nama'] ?? null,
                'jenis_kelamin' => $employee['jk'] ?? null,
                'tempat_lahir' => $employee['tempat_lahir'] ?? null,
                'tanggal_lahir' => !empty($employee['tanggal_lahir']) ? $employee['tanggal_lahir'] : null,
                'status_perkawinan' => $employee['perkawinan'] ?? null,
                'agama' => $employee['agama'] ?? null,
                'kewarganegaraan' => $employee['kewarganegaraan'] ?? null,
                'no_ktp' => $alamat['ktp'] ?? null,
                'no_kk' => $employee['kk'] ?? null,
                'no_wa' => $employee['no_wa'] ?? null,
                'email' => $employee['email'] ?? null,
                'alamat_lengkap_ktp' => $alamat['alamat_ktp'] ?? null,
                'kota_ktp' => $alamat['kota'] ?? null,
                'alamat_lengkap_domisili' => $alamat['domisili'] ?? null,
                'status_active' => '0',
            ]);

            // Simpan Pendidikan
            if (!empty($pendidikan) && is_array($pendidikan)) {
                foreach ($pendidikan as $pend) {
                    EmployeeEducation::create([
                        'employee_id' => $employeeData->id,
                        'jenjang' => $pend['jenjang'] ?? null,
                        'jurusan' => $pend['jurusan'] ?? null,
                        'sekolah_asal' => $pend['sekolah'] ?? null,
                        'institusi' => $pend['sekolah'] ?? null,
                        'tahun_lulus' => $pend['tahun_lulus'] ?? null,
                    ]);
                }
            }

            // Simpan Keluarga
            if (!empty($keluarga) && is_array($keluarga)) {
                foreach ($keluarga as $kel) {
                    $tempatLahir = null;
                    $tanggalLahir = null;
                    
                    if (!empty($kel['ttl'])) {
                        $ttlParts = explode(',', $kel['ttl']);
                        $tempatLahir = trim($ttlParts[0] ?? '');
                        
                        if (isset($ttlParts[1])) {
                            try {
                                $tanggalLahir = \Carbon\Carbon::createFromFormat('d-m-Y', trim($ttlParts[1]))->format('Y-m-d');
                            } catch (\Exception $e) {
                                $tanggalLahir = null;
                            }
                        }
                    }
                    
                    EmployeeFamilyMember::create([
                        'employee_id' => $employeeData->id,
                        'nama' => $kel['nama'] ?? null,
                        'hubungan' => $kel['hubungan'] ?? null,
                        'tempat_lahir' => $tempatLahir,
                        'tanggal_lahir' => $tanggalLahir,
                    ]);
                }
            }

            // Simpan data Kesehatan (jika ada data yang diisi)
            $hasHealthData = !empty($kesehatan['tinggi_badan']) || 
                        !empty($kesehatan['berat_badan']) || 
                        !empty($kesehatan['gol_darah']) ||
                        !empty($kesehatan['tanggal_mcu']) ||
                        !empty($kesehatan['kesimpulan_hasil_mcu']) ||
                        !empty($kesehatan['riwayat_penyakit']);
            
            if ($hasHealthData) {
                EmployeeHealthRecord::create([
                    'employee_id' => $employeeData->id,
                    'tinggi_badan' => !empty($kesehatan['tinggi_badan']) ? $kesehatan['tinggi_badan'] : null,
                    'berat_badan' => !empty($kesehatan['berat_badan']) ? $kesehatan['berat_badan'] : null,
                    'gol_darah' => $kesehatan['gol_darah'] ?? null,
                    'buta_warna' => $kesehatan['buta_warna'] ?? false,
                    'tanggal_mcu' => !empty($kesehatan['tanggal_mcu']) ? $kesehatan['tanggal_mcu'] : null,
                    'kesimpulan_hasil_mcu' => $kesehatan['kesimpulan_hasil_mcu'] ?? null,
                    'riwayat_penyakit' => $kesehatan['riwayat_penyakit'] ?? null,
                ]);
            }

            // Simpan Employment History dari data loker
            EmployeeEmployment::create([
                'employee_id' => $employeeData->id,
                'perusahaan' => $loker->perusahaan_nama ?? 'N/A',
                'penempatan' => $loker->penempatan_nama ?? null,
                'jabatan' => $loker->judul,
                'jenis_kontrak' => $loker->tipe_pekerjaan,
                'pola_kerja' => $loker->jam_kerja,
                'tgl_daftar' => now()->format('Y-m-d'),
                'status' => 'Melamar',
            ]);

            // Simpan Dokumen
            $dokumenData = ['employee_id' => $employeeData->id];
            $dokumenFields = [
                'pas_foto',
                'dokumen_kk',
                'dokumen_surat_pengalaman_kerja',
                'dokumen_ktp',
                'dokumen_bpjs_ketenagakerjaan',
                'dokumen_sio_forklift',
                'dokumen_formulir_bpjs_kesehatan',
                'dokumen_ijazah_terakhir',
                'dokumen_skck',
                'dokumen_lisensi',
            ];

            $hasDocuments = false;
            foreach ($dokumenFields as $field) {
                if ($request->hasFile($field)) {
                    $hasDocuments = true;
                    $file = $request->file($field);
                    $fileName = time() . '_' . uniqid() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('employee_documents/' . $employeeData->id, $fileName, 'public');
                    $dokumenData[$field] = $path;
                }
            }

            if ($hasDocuments) {
                EmployeeDocument::create($dokumenData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil dikirim!',
                'data' => [
                    'employee_id' => $employeeData->id,
                    'nama' => $employeeData->nama,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan lamaran: ' . $e->getMessage()
            ], 500);
        }
    }
}