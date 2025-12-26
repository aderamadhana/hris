<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Employee;
// use App\Models\EmployeePersonal;
// use App\Models\EmployeeAddress;
use App\Models\EmployeeHealth;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmployment;
use App\Models\EmployeeFamily;
use App\Models\ImportLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmployeesImport implements 
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue,
    WithEvents
{
    private $logId;
    private $startTime;
    private $maxExecutionTime = 540; // dalam detik, sesuaikan dengan timeout queue

    public $timeout = 600; // Job timeout
    public $tries = 1; // Jangan retry otomatis

    public function __construct($logId)
    {
        $this->logId = $logId;
        $this->startTime = time();
    }

    public function collection(Collection $rows)
    {
        DB::connection()->disableQueryLog();
        
        // Hitung total hanya sekali
        ImportLog::where('id', $this->logId)->update([
            'total' => $rows->count()
        ]);
        
        $chunks = $rows->chunk(50); // Process 50 rows per batch
        foreach ($chunks as $chunk) {
            if ($this->isTimeout()) {
                Log::warning('Import dihentikan karena timeout', [
                    'log_id' => $this->logId,
                ]);
                
                ImportLog::where('id', $this->logId)->update([
                    'status' => 'timeout',
                    'errors' => DB::raw(
                        "JSON_ARRAY_APPEND(
                            COALESCE(errors, JSON_ARRAY()),
                            '$',
                            JSON_OBJECT(
                                'type', 'timeout',
                                'message', 'Import dihentikan karena timeout. Sebagian data berhasil diimport.'
                            )
                        )"
                    )
                ]);
                break;
            }
            
            DB::beginTransaction();
            try {
                foreach ($chunk as $row) {
                    $this->processRow($row);
                }
                
                DB::commit();
                ImportLog::where('id', $this->logId)->increment('success', $chunk->count());
                
            } catch (\Throwable $e) {
                DB::rollBack();
                
                // Proses ulang satu per satu untuk error tracking
                foreach ($chunk as $row) {
                    DB::beginTransaction();
                    try {
                        $this->processRow($row);
                        DB::commit();
                        ImportLog::where('id', $this->logId)->increment('success');
                    } catch (\Throwable $ex) {
                        DB::rollBack();
                        $this->logError($row, $ex);
                    }
                }
            }
        }
    }

    private function processRow($row)
    {
        // Extract data
        $nrp = trim($row['nrp'] ?? '');
        $nama = trim($row['nama'] ?? '');
        
        if ($nrp === '' || $nama === '') {
            throw new \Exception('NRP atau Nama kosong');
        }
        
        // Basic Info
        $noKtp = trim($row['no_ktp'] ?? '');
        $tempatLahir = trim($row['tempat_lahir'] ?? '');
        $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);
        $email = strtolower(trim($row['e_mail'] ?? ''));
        if ($email === '' || $email === '-' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null;
        }
        $noKontrak = trim($row['no_kontrak'] ?? '');
        $waAktif = trim($row['wa_aktif_tlp'] ?? '');
        $agama = trim($row['agama'] ?? '');
        $statusKary = trim($row['status_kary'] ?? '');
        $statusNikah = trim($row['status_perkawinan'] ?? '');
        $kewarganegaraan = trim($row['kewarganegaraan'] ?? '');
        $jenisKelamin = trim($row['jk'] ?? null);
        $bagian = trim($row['penempatan_bagian'] ?? '');
        $areaKerja = ''; // tidak ada di data JSON
        
        // Address KTP (sama dengan domisili jika tidak ada data KTP terpisah)
        $alamatLengkapKtp = trim($row['alamat_lengkap'] ?? '');
        $desaKtp = trim($row['desa_kelurahan'] ?? '');
        $kecamatanKtp = trim($row['kecamatan'] ?? '');
        $kotaKtp = trim($row['kota_kabupaten'] ?? '');
        $kodePosKtp = trim($row['kode_pos'] ?? '');
        
        // Address Domisili
        $alamatLengkapDomisili = trim($row['alamat_lengkap'] ?? '');
        $desaDomisili = trim($row['desa_kelurahan'] ?? '');
        $kecamatanDomisili = trim($row['kecamatan'] ?? '');
        $kotaDomisili = trim($row['kota_kabupaten'] ?? '');
        $kodePosDomisili = trim($row['kode_pos'] ?? '');
        
        // Personal
        $noKk = trim($row['no_kartu_keluarga'] ?? '');
        $npwp = trim($row['npwp'] ?? '');
        $bpjsTk = trim($row['bpjs_tk'] ?? '');
        $xTk = trim($row['x'] ?? '');
        $bpjsKes = trim($row['bpjs_kes'] ?? '');
        $xKs = trim($row['x_ks'] ?? '');
        $namaFaskes = trim($row['nama_faskes'] ?? '');
        $noSkck = trim($row['catatan_kepolisian_skck'] ?? '');
        $masaBerlakuSkck = $this->parseDate($row['masa_berlaku'] ?? null);
        $jenisLisensi = trim($row['jenis_lisensi'] ?? '');
        $noLisensi = trim($row['no_lisensi_sio_sim_lainnya'] ?? '');
        $masaBerlakuLisensi = trim($row['berlaku'] ?? '');
        $noRekening = trim($row['no_rekening'] ?? '');
        $noCif = trim($row['no_cif'] ?? '');
        $bank = trim($row['bank'] ?? '');
        $ptkp = trim($row['ptkp'] ?? '');
        
        $shoeSizeRaw = trim($row['shoes_size'] ?? '');
        $shoeSize = $shoeSizeRaw === '' ? null : $shoeSizeRaw;
        $uniformSize = trim($row['uniform_size'] ?? '');
        $gp = trim($row['gp'] ?? '');
        $via = trim($row['via'] ?? '');
        $regDigantikan = trim($row['reg_digantikan'] ?? '');
        $namaDigantikan = trim($row['nama_digantikan'] ?? '');
        
        // Health
        $tglMcu = $this->parseDate($row['tgl_mcu'] ?? null);
        $tinggiBadan = $this->parseInt($row['tb'] ?? null);
        $beratBadan = $this->parseInt($row['bb'] ?? null);
        $golDarah = trim($row['gd'] ?? '');
        $butaWarnaRaw = trim($row['buta_warna'] ?? '');
        $riwayatSakit = trim($row['riwayat_sakit'] ?? '');
        $tglDrugTest = $this->parseDate($row['tgl_drug_test'] ?? null);
        $hasilDrugTest = trim($row['drug_test'] ?? '');
        
        $darah = trim($row['darah'] ?? '');
        $urine = trim($row['urine'] ?? '');
        $f_hati = trim($row['f_hati'] ?? '');
        $gula_darah = trim($row['gula_darah_creatinin'] ?? '');
        $ginjal = trim($row['f_ginjal_puasa'] ?? '');
        $thorax = trim($row['thorax'] ?? '');
        $tensi = trim($row['tensi'] ?? '');
        $nadi = trim($row['nadi'] ?? '');
        $od = trim($row['od'] ?? '');
        $os = trim($row['os'] ?? '');
        
        // Education
        $pendidikan = trim($row[''] ?? ''); // kolom kosong di JSON untuk pendidikan
        $jurusan = trim($row['jurusan'] ?? '');
        $sekolahAsal = trim($row['sekolah_asal'] ?? '');
        $tahunLulus = $this->parseYear($row['tahun_lulus'] ?? null);
        
        // Employment
        $perusahaan = trim($row['perusahaan'] ?? '');
        $penempatan = trim($row['penempatan_bagian'] ?? '');
        $costCenter = trim($row['cost_center'] ?? '');
        $tglDaftar = $this->parseDate($row['tgl_daftar'] ?? null);
        $tglAwalKerja = $this->parseDate($row['tgl_awal_kerja'] ?? null);
        $tglAkhirKerja = $this->parseDate($row['tgl_akhir_kerja'] ?? null);
        $jenisKontrak = trim($row['jenis_kontrak'] ?? '');
        $statusKerja = trim($row['status'] ?? '');
        $jobRole = trim($row['job_roll'] ?? '');
        $masaKerja = trim($row['masa_kerja'] ?? '');
        $polaKerja = trim($row['pola_kerja'] ?? '');
        $jenisKerja = trim($row['jenis_kerja'] ?? '');
        $hariKerja = trim($row['hari_kerja'] ?? '');
        $keterangan_status = trim($row['keterangan_status'] ?? '');
        
        // Family
        $namaAyah = trim($row['nama_ayah_kandung'] ?? '');
        $namaIbu = trim($row['nama_ibu_kandung'] ?? '');
        $namaPasangan = trim($row['suami_istri'] ?? '');
        $noKtpPasangan = trim($row['no_ktp_suamiistri'] ?? '');
        $jkPasangan = trim($row['jk_istri'] ?? '');
        $tempatLahirPasangan = trim($row['tempat_lahir_suamiistri'] ?? '');
        $tglLahirPasangan = $this->parseDate($row['tanggal_lahir_suamiistri'] ?? null);
        $pendidikanPasangan = trim($row['pendidikan'] ?? '');
        $pekerjaanPasangan = trim($row['pekerjaan'] ?? '');
        $tglPerkawinan = $this->parseDate($row['tgl_perkawinan'] ?? null);
        $anak1 = trim($row['anak_ke_1'] ?? '');
        $anak2 = trim($row['anak_ke_2'] ?? '');
        $anak3 = trim($row['anak_ke_3'] ?? '');
        
        // Create/Update User
        $userId = null;
        $status_active = '0';
        
        // Create/Update Employee (dengan data personal dan address digabung)
        $employee = Employee::updateOrCreate(
            ['nrp' => $nrp],
            [
                'user_id' => $userId,
                'nama' => $nama,
                'bagian' => $bagian,
                'area_kerja' => $areaKerja,
                'jenis_kelamin' => $jenisKelamin,
                'status_active' => $status_active,
                'status_kary' => $statusKary,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $agama,
                'status_perkawinan' => $statusNikah,
                'kewarganegaraan' => $kewarganegaraan,
                
                // Personal data
                'no_ktp' => $noKtp,
                'no_kk' => $noKk,
                'no_wa' => $waAktif,
                'bpjs_tk' => $bpjsTk,
                'x' => $xTk,
                'bpjs_kes' => $bpjsKes,
                'x_ks' => $xKs,
                'nama_faskes' => $namaFaskes,
                'email' => $email,
                'no_skck' => $noSkck,
                'masa_berlaku_skck' => $masaBerlakuSkck,
                'jenis_lisensi' => $jenisLisensi,
                'no_lisensi' => $noLisensi,
                'masa_berlaku_lisensi' => $masaBerlakuLisensi,
                'no_rekening' => $noRekening,
                'no_cif' => $noCif,
                'bank' => $bank,
                'npwp' => $npwp,
                'ptkp' => $ptkp,
                'shoe_size' => $shoeSize,
                'uniform_size' => $uniformSize,
                'gp' => $gp,
                'via' => $via,
                'reg_digantikan' => $regDigantikan,
                'nama_digantikan' => $namaDigantikan,
                
                // Address KTP
                'alamat_lengkap_ktp' => $alamatLengkapKtp,
                'desa_ktp' => $desaKtp,
                'kecamatan_ktp' => $kecamatanKtp,
                'kota_ktp' => $kotaKtp,
                'kode_pos_ktp' => $kodePosKtp,
                
                // Address Domisili
                'alamat_lengkap_domisili' => $alamatLengkapDomisili,
                'desa_domisili' => $desaDomisili,
                'kecamatan_domisili' => $kecamatanDomisili,
                'kota_domisili' => $kotaDomisili,
                'kode_pos_domisili' => $kodePosDomisili,
            ]
        );

        if ($noKontrak !== '' && $noKtp !== '') {
            try {
                $user = User::updateOrCreate(
                    ['no_ktp' => $noKtp],
                    [
                        'email' => $email ?: null,
                        'name' => $nama,
                        'password' => Hash::make($noKtp),
                        'role_id' => 2,
                        'employee_id' => $employee->id,
                    ]
                );

                $employee->update([
                    'user_id' => $user->id,
                    'status_active' => $noKontrak !== '' ? 1 : 0,
                ]);
            } catch (QueryException $e) {
                if ($e->getCode() === '23000') {
                    $user = User::where('no_ktp', $noKtp)->first();
                    if ($user) {
                        $userId = $user->id;
                        $status_active = '1';
                        
                        // Update user dengan data terbaru
                        $user->update([
                            'name' => $nama,
                            'no_ktp' => $noKtp,
                            'employee_id' => $employee->id,
                        ]);
                        
                        // Update employee dengan user_id dan status_active
                        $employee->update([
                            'user_id' => $userId,
                            'status_active' => $status_active,
                        ]);
                    }
                } else {
                    throw $e;
                }
            }
        }
        
        // Health Record
        EmployeeHealth::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'tanggal_mcu' => $tglMcu,
                'tinggi_badan' => $tinggiBadan,
                'berat_badan' => $beratBadan,
                'gol_darah' => $golDarah,
                'buta_warna' => !in_array(strtolower($butaWarnaRaw), ['normal', 'tidak', 'n', '-', '']),
                'riwayat_penyakit' => $riwayatSakit,
                'tanggal_drug_test' => $tglDrugTest,
                'hasil_drug_test' => $hasilDrugTest,
                'darah' => $darah,
                'urine' => $urine,
                'f_hati' => $f_hati,
                'gula_darah' => $gula_darah,
                'ginjal' => $ginjal,
                'thorax' => $thorax,
                'tensi' => $tensi,
                'nadi' => $nadi,
                'od' => $od,
                'os' => $os,
            ]
        );
        
        // Education
        if (!empty($pendidikan)) {
            EmployeeEducation::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'jenjang' => $pendidikan,
                ],
                [
                    'jurusan' => $jurusan,
                    'institusi' => $sekolahAsal,
                    'tahun_lulus' => $tahunLulus,
                    'sekolah_asal' => $sekolahAsal,
                ]
            );
        }
        
        // Employment History
        EmployeeEmployment::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'perusahaan' => $perusahaan,
                'tgl_awal_kerja' => $tglAwalKerja,
            ],
            [
                'jabatan' => $jobRole,
                'penempatan' => $penempatan,
                'tgl_akhir_kerja' => $tglAkhirKerja,
                'jenis_kontrak' => $jenisKontrak,
                'status' => $statusKerja,
                'no_kontrak' => $noKontrak,
                'cost_center' => $costCenter,
                'tgl_daftar' => $tglDaftar,
                'keterangan_status' => $keterangan_status,
                'job_roll' => $jobRole,
                'masa_kerja' => $masaKerja,
                'pola_kerja' => $polaKerja,
                'jenis_kerja' => $jenisKerja,
                'hari_kerja' => $hariKerja,
            ]
        );
        
        // Family Members
        if (!empty($namaAyah)) {
            EmployeeFamily::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'hubungan' => 'Ayah',
                ],
                [
                    'nama' => $namaAyah,
                ]
            );
        }
        
        if (!empty($namaIbu)) {
            EmployeeFamily::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'hubungan' => 'Ibu',
                ],
                [
                    'nama' => $namaIbu,
                ]
            );
        }
        
        if (!empty($namaPasangan)) {
            $hubunganPasangan = strtolower($jenisKelamin) === 'laki-laki' ? 'Istri' : 'Suami';
            EmployeeFamily::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'hubungan' => $hubunganPasangan,
                ],
                [
                    'nama' => $namaPasangan,
                    'tempat_lahir' => $tempatLahirPasangan,
                    'tanggal_lahir' => $tglLahirPasangan,
                    'pendidikan' => $pendidikanPasangan,
                    'pekerjaan' => $pekerjaanPasangan,
                    'tgl_perkawinan' => $tglPerkawinan,
                ]
            );
        }
        
        $anakList = [$anak1, $anak2, $anak3];
        foreach ($anakList as $index => $anak) {
            if (!empty($anak)) {
                EmployeeFamily::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'hubungan' => 'Anak',
                        'nama' => $anak,
                    ],
                    [
                        'nama' => $anak,
                        'hubungan' => 'Anak',
                    ]
                );
            }
        }
    }
    private function logError($row, \Throwable $e)
    {
        $nrp = trim($row['nrp'] ?? '');
        $email = trim($row['e_mail'] ?? '');
        $noKtp = trim($row['no_ktp'] ?? '');

        $errorMessage = $e->getMessage();

        if (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'no_ktp_unique')) {
            $errorMessage = "No KTP {$noKtp} sudah terdaftar di sistem";
        }
        // Atau cek error duplicate lainnya
        elseif (str_contains($errorMessage, 'Duplicate entry')) {
            // Extract nilai yang duplicate dari pesan error
            preg_match("/Duplicate entry '(.+?)' for key '(.+?)'/", $errorMessage, $matches);
            $duplicateValue = $matches[1] ?? 'unknown';
            $duplicateKey = $matches[2] ?? 'unknown';
            
            $errorMessage = "Data duplikat: {$duplicateValue} {$duplicateKey} sudah ada";
        }
        
        ImportLog::where('id', $this->logId)->increment('failed');
        ImportLog::where('id', $this->logId)->update([
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'nrp', '".addslashes($nrp)."',
                        'email', '".addslashes($email)."',
                        'error', '".addslashes($errorMessage)."'
                    )
                )"
            )
        ]);
        
        Log::error('Import employee gagal', [
            'nrp' => $nrp,
            'email' => $email,
            'error' => $e->getMessage(),
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                // Hanya update ke completed jika tidak timeout
                $log = ImportLog::find($this->logId);
                if ($log && $log->status !== 'timeout') {
                    ImportLog::where('id', $this->logId)->update([
                        'status' => 'completed',
                    ]);
                }
            },
        ];
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'nama'  => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 50;
    }

    /**
     * Cek apakah sudah mendekati timeout
     */
    private function isTimeout(): bool
    {
        $elapsed = time() - $this->startTime;
        return $elapsed >= $this->maxExecutionTime;
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        ImportLog::where('id', $this->logId)->update([
            'status' => 'failed',
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'type', 'job_failed',
                        'error', '".addslashes($exception->getMessage())."'
                    )
                )"
            )
        ]);

        Log::error('Import job failed', [
            'log_id' => $this->logId,
            'error' => $exception->getMessage()
        ]);
    }

    private function parseDate($value)
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);

        if ($raw === '' || $raw === '-' || strtoupper($raw) === 'N/A' || strtoupper($raw) === 'NA') {
            return null;
        }

        try {
            if (is_numeric($raw)) {
                if ((int) $raw <= 0) {
                    return null;
                }

                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $raw);
            }

            return \Carbon\Carbon::parse($raw);

        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseYear($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && $value > 1000) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return (int) $date->format('Y');
            } catch (\Exception $e) {
                return null;
            }
        }

        if (preg_match('/(19|20)\d{2}/', (string) $value, $match)) {
            $year = (int) $match[0];
            return ($year >= 1901 && $year <= 2155) ? $year : null;
        }

        return null;
    }

    private function parseInt($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }
}