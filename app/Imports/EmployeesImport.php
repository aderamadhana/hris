<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeePersonal;
use App\Models\EmployeeAddress;
use App\Models\EmployeeHealth;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmployment;
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

    public function __construct($logId)
    {
        $this->logId = $logId;
    }

    public function collection(Collection $rows)
    {
        // Log::info($rows);
        ImportLog::where('id', $this->logId)->increment('total');
        foreach ($rows as $row) {
            $nrp            = trim($row['nrp'] ?? '');
            $userIdExcel   = trim($row['user_id'] ?? '');
            $nama           = trim($row['nama'] ?? '');
            $jkRaw          = trim($row['jk'] ?? '');
            $noKtp          = trim($row['no_ktp'] ?? '');
            $tempatLahir   = trim($row['tempat_lahir'] ?? '');
            $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);

            // Log::info('Parsed tanggal lahir', [
            //     'raw' => $row['tanggal_lahir'],
            //     'parsed' => optional($tanggalLahir)->format('Y-m-d'),
            // ]);

            $jalan          = trim($row['jl_gg_dsn_dkh_perum'] ?? '');
            $rt             = trim($row['rt'] ?? '');
            $rw             = trim($row['rw'] ?? '');
            $desa           = $row['desa_kelurahan'] ?? null;

            $kecamatan      = trim($row['kecamatan'] ?? '');
            $kota           = trim($row['kota_kabupaten'] ?? '');
            $kodePos        = trim($row['kode_pos'] ?? '');

            $alamatLengkap  = trim($row['alamat_lengkap'] ?? '');
            $alamatTipe     = trim($row['alamat_tinggal_asal'] ?? 'Domisili');

            $waAktif        = trim($row['wa_aktif_tlp'] ?? '');
            $email          = strtolower(trim($row['e_mail'] ?? ''));

            $bpjsTk         = trim($row['bpjs_tk'] ?? '');
            $bpjsKes        = trim($row['bpjs_kes'] ?? '');
            $bpjsKs         = trim($row['ks'] ?? '');

            $npwp           = trim($row['npwp'] ?? '');
            $ptkp           = trim($row['ptkp'] ?? '');

            $agama          = trim($row['agama'] ?? '');
            $statusNikah    = trim($row['status_perkawinan'] ?? '');
            $kewarganegaraan= trim($row['kewarganegaraan'] ?? '');


            $tglMcu       = $this->parseDate($row['tgl_mcu'] ?? null);

            $tinggiBadan = $this->parseInt($row['tb'] ?? null);
            $beratBadan  = $this->parseInt($row['bb'] ?? null);
            $golDarah       = trim($row['darah'] ?? '');
            $urine          = trim($row['urine'] ?? '');
            $fungsiHati     = trim($row['f_hati'] ?? '');
            $gulaDarah      = trim($row['gula_darah_creatinin'] ?? '');
            $fungsiGinjal   = trim($row['f_ginjal_puasa'] ?? '');
            $thorax         = trim($row['thorax'] ?? '');

            $tensi          = trim($row['tensi'] ?? '');
            $nadi           = trim($row['nadi'] ?? '');

            $butaWarnaRaw   = trim($row['buta_warna'] ?? '');
            $mataOD         = trim($row['od'] ?? '');
            $mataOS         = trim($row['os'] ?? '');

            $riwayatSakit   = trim($row['riwayat_sakit'] ?? '');

            $tglDrugTest  = $this->parseDate($row['tgl_drug_test'] ?? null);

            $hasilDrugTest  = trim($row['drug_test'] ?? '');

            $noKk           = trim($row['no_kartu_keluarga'] ?? '');

            $namaAyah       = trim($row['nama_ayah_kandung'] ?? '');
            $namaIbu        = trim($row['nama_ibu_kandung'] ?? '');

            $namaPasangan   = trim($row['suami_istri'] ?? '');
            $ktpPasangan    = trim($row['no_ktp_suami_istri'] ?? '');
            $jkPasangan     = trim($row['jk_istri'] ?? '');

            $tempatLahirPasangan = trim($row['tempat_lahir_suami_istri'] ?? '');
            $tglLahirPasangan    = $this->parseDate($row['tanggal_lahir_suami_istri'] ?? null);

            $tglNikah       = $this->parseDate($row['tgl_perkawinan'] ?? null);

            $anak1          = trim($row['anak_ke_1'] ?? '');
            $anak2          = trim($row['anak_ke_2'] ?? '');
            $anak3          = trim($row['anak_ke_3'] ?? '');
            $totalAnak      = trim($row['anak'] ?? '');

            $pendidikan     = $row['pendidikan'] ?? null;
            $jurusan        = trim($row['jurusan'] ?? '');
            $sekolahAsal    = $row['sekolah_asal'] ?? null;
            $tahunLulus     = $this->parseYear($row['tahun_lulus']);

            $perusahaan     = trim($row['perusahaan'] ?? '');
            $penempatan     = trim($row['penempatan_bagian'] ?? '');
            $noKontrak      = trim($row['no_kontrak'] ?? '');
            $costCenter     = trim($row['cost_center'] ?? '');

            $tglDaftar      = $this->parseDate($row['tgl_daftar'] ?? null);

            $tglAwalKerja = $this->parseDate($row['tgl_awal_kerja'] ?? null);

            $tglAkhirKerja  =  $this->parseDate($row['tgl_akhir_kerja'] ?? null);

            $jenisKontrak   = trim($row['jenis_kontrak'] ?? '');
            $statusKerja   = trim($row['status'] ?? '');
            $jobRole        = trim($row['job_roll'] ?? '');

            $usia           = trim($row['usia'] ?? '');
            $masaKerja      = trim($row['masa_kerja'] ?? '');
            $polaKerja      = trim($row['pola_kerja'] ?? '');
            $jenisKerja     = trim($row['jenis_kerja'] ?? '');
            $hariKerja      = trim($row['hari_kerja'] ?? '');

            $shoeSize       = trim($row['shoes_size'] ?? '');
            $uniformSize    = trim($row['uniform_size'] ?? '');
            $gp             = trim($row['gp'] ?? '');
            $via            = trim($row['via'] ?? '');

            $regDiganti     = trim($row['reg_digantikan'] ?? '');
            $namaDiganti    = trim($row['nama_digantikan'] ?? '');

            $isActiveRaw    = trim($row['1_0'] ?? '');
            $keterangan     = trim($row['keterangan'] ?? '');

            if ($nrp === '' || $nama === '') {
                 Log::warning('Row dilewati: NRP/Nama kosong', $row->toArray());
                continue;
            }

            try {
                DB::beginTransaction();

                /** USER */
                $userId = null;
                $status_active = 0;
                if ($noKontrak !== '' && $email !== '') {
                    $emailKey = strtolower($email);

                    try {
                        $user = User::updateOrCreate(
                            ['email' => $emailKey],
                            [
                                'name'     => $nama,
                                'password' => Hash::make('123456'),
                                'role_id'  => 2,
                            ]
                        );
                    } catch (QueryException $e) {
                        if ($e->getCode() === '23000') {
                            $user = User::where('email', $emailKey)->first();
                        } else {
                            throw $e;
                        }
                    }

                    $userId = $user?->id;
                    $status_active = 1;
                }

                /** EMPLOYEE */
                $employee = Employee::updateOrCreate(
                    ['nrp' => $nrp],
                    [
                        'user_id' => $userId,
                        'nama'    => $nama,
                        'status_active'    => $status_active,
                        'tempat_lahir' => $tempatLahir,
                        'tanggal_lahir' => optional($tanggalLahir)->format('Y-m-d'),
                        'agama' => $agama,
                        'status_perkawinan' => $statusNikah,
                        'kewarganegaraan' => $kewarganegaraan
                    ]
                );

                /** PERSONAL */
                EmployeePersonal::updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'no_ktp' => $noKtp,
                        'no_kk'  => $noKk,
                        'npwp'   => $npwp,
                    ]
                );

                /** ADDRESS (DOMISILI) */
                EmployeeAddress::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'tipe'        => 'Domisili'
                    ],
                    [
                        'alamat_lengkap'=> $alamatLengkap,
                        'desa'          => $desa,
                        'kecamatan'     => $kecamatan,
                        'kota'          => $kota,
                        'kode_pos'      => $kodePos,
                    ]
                );

                /** HEALTH */
                EmployeeHealth::updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'tanggal_mcu'        => $tglMcu,
                        'tinggi_badan'       => $tinggiBadan,
                        'berat_badan'        => $beratBadan,
                        'gol_darah'          => $golDarah,
                        'buta_warna'         => in_array(strtoupper($butaWarnaRaw), ['YA','Y','1','TRUE']),
                        'riwayat_penyakit'   => $riwayatSakit,
                        'tanggal_drug_test'  => $tglDrugTest,
                        'hasil_drug_test'    => $hasilDrugTest,
                    ]
                );

                /** EDUCATION */
                EmployeeEducation::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'jenjang'     => $pendidikan
                    ],
                    [
                        'jurusan'     => $jurusan,
                        'institusi'   => $sekolahAsal,
                        'tahun_lulus' => $tahunLulus,
                    ]
                );

                /** EMPLOYMENT HISTORY */
                EmployeeEmployment::updateOrCreate(
                    [
                        'employee_id'    => $employee->id,
                        'perusahaan'     => $perusahaan,
                        'tgl_awal_kerja' => $tglAwalKerja,
                    ],
                    [
                        'jabatan'        => $jobRole,
                        'penempatan'     => $penempatan,
                        'tgl_akhir_kerja'=> $tglAkhirKerja,
                        'jenis_kontrak'  => $jenisKontrak,
                        'status'         => $statusKerja,
                    ]
                );


                DB::commit();

                ImportLog::where('id', $this->logId)->increment('success');
            } catch (\Throwable $e) {
                DB::rollBack();

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
                    'nrp'   => $nrp,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }

            // ✅ cek selesai SETIAP ROW
            // $log = ImportLog::find($this->logId);

            // if (($log->success + $log->failed) >= $log->expected_total) {
            //     ImportLog::where('id', $this->logId)->update([
            //         'status' => 'completed'
            //     ]);
            // }
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                ImportLog::where('id', $this->logId)->update([
                    'status' => 'completed',
                ]);
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
        return 200;
    }

    private function parseDate($value)
    {
        // kosong / placeholder
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);

        if ($raw === '' || $raw === '-' || strtoupper($raw) === 'N/A' || strtoupper($raw) === 'NA') {
            return null;
        }

        try {
            // Excel serial number
            if (is_numeric($raw)) {
                // 0 atau negatif tidak valid
                if ((int) $raw <= 0) {
                    return null;
                }

                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $raw);
            }

            // string tanggal normal
            return \Carbon\Carbon::parse($raw);

        } catch (\Throwable $e) {
            // terakhir: anggap NULL, jangan error
            return null;
        }
    }



    private function parseYear($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel serial date
        if (is_numeric($value) && $value > 1000) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return (int) $date->format('Y');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Ambil 4 digit tahun dari string
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
