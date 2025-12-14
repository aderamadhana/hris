<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeePersonal;
use App\Models\EmployeeAddress;
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
        
        $noKtp = trim($row['no_ktp'] ?? '');
        $tempatLahir = trim($row['tempat_lahir'] ?? '');
        $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);
        $email = strtolower(trim($row['e_mail'] ?? ''));
        $noKontrak = trim($row['no_kontrak'] ?? '');
        $waAktif = trim($row['wa_aktif_tlp'] ?? '');
        $agama = trim($row['agama'] ?? '');
        $statusNikah = trim($row['status_perkawinan'] ?? '');
        $kewarganegaraan = trim($row['kewarganegaraan'] ?? '');
        
        // Address
        $alamatLengkap = trim($row['alamat_lengkap'] ?? '');
        $desa = $row['desa_kelurahan'] ?? null;
        $kecamatan = trim($row['kecamatan'] ?? '');
        $kota = trim($row['kota_kabupaten'] ?? '');
        $kodePos = trim($row['kode_pos'] ?? '');
        
        // Personal
        $noKk = trim($row['no_kartu_keluarga'] ?? '');
        $npwp = trim($row['npwp'] ?? '');
        $bpjsTk = trim($row['bpjs_tk'] ?? '');
        $bpjsKes = trim($row['bpjs_kes'] ?? '');
        $namaFaskes = trim($row['nama_faskes'] ?? '');
        $noSkck = trim($row['catatan_kepolisian_skck'] ?? '');
        $masaBerlakuSkck = $this->parseDate($row['masa_berlaku'] ?? null);
        $jenisLisensi = trim($row['jenis_lisensi'] ?? '');
        $noLisensi = trim($row['no_lisensi_sio_sim_lainnya'] ?? '');
        $masaBerlakuLisensi = $this->parseDate($row['berlaku'] ?? null);
        $noRekening = trim($row['no_rekening'] ?? '');
        $bank = trim($row['bank'] ?? '');
        $ptkp = trim($row['ptkp'] ?? '');
        
        $shoeSizeRaw = trim($row['shoes_size'] ?? '');
        $shoeSize = $shoeSizeRaw === '' ? null : (int) $shoeSizeRaw;
        $uniformSize = trim($row['uniform_size'] ?? '');
        
        // Health
        $tglMcu = $this->parseDate($row['tgl_mcu'] ?? null);
        $tinggiBadan = $this->parseInt($row['tb'] ?? null);
        $beratBadan = $this->parseInt($row['bb'] ?? null);
        $golDarah = trim($row['darah'] ?? '');
        $butaWarnaRaw = trim($row['buta_warna'] ?? '');
        $riwayatSakit = trim($row['riwayat_sakit'] ?? '');
        $tglDrugTest = $this->parseDate($row['tgl_drug_test'] ?? null);
        $hasilDrugTest = trim($row['drug_test'] ?? '');
        
        $darah = trim($row['darah'] ?? '');
        $urine = trim($row['urine'] ?? '');
        $f_hati = trim($row['f_hati'] ?? '');
        $gula_darah = trim($row['gula_darah'] ?? '');
        $ginjal = trim($row['ginjal'] ?? '');
        $thorax = trim($row['thorax'] ?? '');
        $tensi = trim($row['tensi'] ?? '');
        $nadi = trim($row['nadi'] ?? '');
        $od = trim($row['od'] ?? '');
        $os = trim($row['os'] ?? '');
        
        // Education
        $pendidikan = $row['pendidikan'] ?? null;
        $jurusan = trim($row['jurusan'] ?? '');
        $sekolahAsal = $row['sekolah_asal'] ?? null;
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
        $tempatLahirPasangan = trim($row['tempat_lahir_suamiistri'] ?? '');
        $tglLahirPasangan = $this->parseDate($row['tanggal_lahir_suamiistri'] ?? null);
        $anak1 = trim($row['anak_ke_1'] ?? '');
        $anak2 = trim($row['anak_ke_2'] ?? '');
        $anak3 = trim($row['anak_ke_3'] ?? '');
        
        // Create/Update User
        $userId = null;
        $status_active = 0;
        
        if ($noKontrak !== '' && $email !== '') {
            try {
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => Hash::make($noKtp),
                        'role_id' => 2,
                    ]
                );
                $userId = $user->id;
                $status_active = 1;
            } catch (QueryException $e) {
                if ($e->getCode() === '23000') {
                    $user = User::where('email', $email)->first();
                    $userId = $user?->id;
                    $status_active = 1;
                } else {
                    throw $e;
                }
            }
        }
        
        // Create/Update Employee
        $employee = Employee::updateOrCreate(
            ['nrp' => $nrp],
            [
                'user_id' => $userId,
                'nama' => $nama,
                'status_active' => $status_active,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $agama,
                'status_perkawinan' => $statusNikah,
                'kewarganegaraan' => $kewarganegaraan
            ]
        );
        
        // Personal
        EmployeePersonal::updateOrInsert(
            ['employee_id' => $employee->id],
            [
                'no_ktp' => $noKtp,
                'no_kk' => $noKk,
                'npwp' => $npwp,
                'no_wa' => $waAktif,
                'bpjs_tk' => $bpjsTk,
                'bpjs_kes' => $bpjsKes,
                'nama_faskes' => $namaFaskes,
                'email' => $email,
                'no_skck' => $noSkck,
                'masa_berlaku_skck' => $masaBerlakuSkck,
                'jenis_lisensi' => $jenisLisensi,
                'no_lisensi' => $noLisensi,
                'masa_berlaku_lisensi' => $masaBerlakuLisensi,
                'no_rekening' => $noRekening,
                'bank' => $bank,
                'ptkp' => $ptkp,
                'shoe_size' => $shoeSize,
                'uniform_size' => $uniformSize,
            ]
        );
        
        // Address
        EmployeeAddress::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'tipe' => 'Domisili'
            ],
            [
                'alamat_lengkap' => $alamatLengkap,
                'desa' => $desa,
                'kecamatan' => $kecamatan,
                'kota' => $kota,
                'kode_pos' => $kodePos,
            ]
        );
        
        // Health
        EmployeeHealth::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'tanggal_mcu' => $tglMcu,
                'tinggi_badan' => $tinggiBadan,
                'berat_badan' => $beratBadan,
                'gol_darah' => $golDarah,
                'buta_warna' => in_array(strtoupper($butaWarnaRaw), ['YA','Y','1','TRUE']),
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
        
        // Employment
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
        
        // Family
        if (!empty($namaAyah)) {
            EmployeeFamily::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'hubungan' => 'ayah',
                ],
                [
                    'nama' => $namaAyah,
                    'tanggal_lahir' => $tglLahirPasangan,
                    'tempat_lahir' => $tempatLahirPasangan
                ]
            );
        }
        
        if (!empty($namaIbu)) {
            EmployeeFamily::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'hubungan' => 'ibu',
                ],
                [
                    'nama' => $namaIbu,
                    'tanggal_lahir' => $tglLahirPasangan,
                ]
            );
        }
        
        $anakList = [$anak1, $anak2, $anak3];
        foreach ($anakList as $anak) {
            if (!empty($anak)) {
                EmployeeFamily::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'hubungan' => 'anak',
                        'nama' => $anak,
                    ],
                    [
                        'nama' => $anak,
                        'hubungan' => 'anak',
                    ]
                );
            }
        }
    }

    private function logError($row, \Throwable $e)
    {
        $nrp = trim($row['nrp'] ?? '');
        $email = trim($row['e_mail'] ?? '');
        
        ImportLog::where('id', $this->logId)->increment('failed');
        ImportLog::where('id', $this->logId)->update([
            'errors' => DB::raw(
                "JSON_ARRAY_APPEND(
                    COALESCE(errors, JSON_ARRAY()),
                    '$',
                    JSON_OBJECT(
                        'nrp', '".addslashes($nrp)."',
                        'email', '".addslashes($email)."',
                        'error', '".addslashes($e->getMessage())."'
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