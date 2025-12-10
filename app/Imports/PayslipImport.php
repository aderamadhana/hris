<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeePersonal;
use App\Models\PayrollPeriod;
use App\Models\SalaryConfiguration;
use App\Models\AttendanceSummary;
use App\Models\OvertimeSummary;
use App\Models\Earning;
use App\Models\Allowance;
use App\Models\AdditionalEarning;
use App\Models\Deduction;
use App\Models\PayrollSummary;
// use App\Models\PayrollImportLog; // Model baru untuk tracking

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PayslipImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithChunkReading, 
    WithBatchInserts,
    WithCalculatedFormulas,
    ShouldQueue
{
    protected $payrollPeriodId;
    protected $importId; // Unique ID untuk tracking import ini

    public function __construct($payrollPeriodId, $importId = null)
    {
        $this->payrollPeriodId = $payrollPeriodId;
        $this->importId = $importId ?? uniqid('import_', true);
        
        // Initialize counters di cache
        Cache::put("import_{$this->importId}_success", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_failed", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_errors", [], now()->addHours(24));
    }

    public function collection(Collection $rows)
    {
        Log::info("Processing chunk with " . $rows->count() . " rows for import ID: {$this->importId}");
        
        foreach ($rows as $index => $row) {
            $nik = null;
            
            try {
                DB::beginTransaction();

                // 1. Cek NIK di employee_personals dan get employee_id
                $nik = (string) ((int) $row['nik']);
                
                Log::info("Processing NIK: {$nik}");
                
                $employeePersonal = EmployeePersonal::where('no_ktp', $nik)->first();
                
                if (!$employeePersonal) {
                    DB::rollBack();
                    
                    $error = "NIK {$nik} tidak ditemukan di employee_personals";
                    Log::warning($error);
                    
                    $this->incrementFailed($error);
                    continue;
                }

                $employeeId = $employeePersonal->employee_id;

                // 2. Validasi employee exists
                $employee = Employee::find($employeeId);
                if (!$employee) {
                    DB::rollBack();
                    
                    $error = "Employee ID {$employeeId} tidak ditemukan";
                    Log::warning($error);
                    
                    $this->incrementFailed($error);
                    continue;
                }

                // 3. Update atau create employee data (jika ada perubahan)
                $employee->update([
                    'nik_kary' => $row['nik_kary'] ?? null,
                    'no_rek' => $row['no_rek'] ?? null,
                    'nama' => $row['nama'] ?? null,
                    'status_kary' => $row['status_kary'] ?? null,
                    'bagian' => $row['bagian'] ?? null,
                    'area_kerja' => $row['area_kerja'] ?? null,
                ]);

                // 4. Salary Configuration (optional, jika ada data)
                if (isset($row['gaji_pokok']) || isset($row['gaji_per_hari'])) {
                    SalaryConfiguration::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'effective_date' => now()->startOfMonth(),
                        ],
                        [
                            'gaji_pokok' => $row['gaji_pokok'] ?? null,
                            'gaji_per_hari' => $row['gaji_per_hari'] ?? null,
                            'gaji_train_hk' => $row['gaji_train_hk'] ?? null,
                            'gaji_train_upah_per_jam' => $row['gaji_train_upah_per_jam'] ?? null,
                            'lembur_per_hari' => $row['lembur_per_hari'] ?? null,
                            'lembur_per_jam' => $row['lembur_per_jam'] ?? null,
                        ]
                    );
                }

                // 5. Attendance Summary
                AttendanceSummary::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'jam_kerja' => $row['jam_kerja'] ?? null,
                        'jam_hk' => $row['jam_hk'] ?? null,
                        'jam_hl' => $row['jam_hl'] ?? null,
                        'jam_hr' => $row['jam_hr'] ?? null,
                        'jml_hl' => $row['jml_hl'] ?? null,
                        'jml_hr' => $row['jml_hr'] ?? null,
                        'hadir' => $row['hadir'] ?? null,
                        'mangkir_hari' => $row['mangkir_hari'] ?? null,
                        'pot_tdk_masuk_hari' => $row['pot_tdk_masuk_hari'] ?? null,
                        'terlambat_hari' => $row['terlambat_hari'] ?? null,
                        'terlambat_menit' => $row['terlambat_menit'] ?? null,
                        'terlambat_jam' => $row['terlambat_jam'] ?? null,
                        'ijin_pulang' => $row['ijin_pulang'] ?? null,
                        'cuti_dibayar' => $row['cuti_dibayar'] ?? null,
                    ]
                );

                // 6. Overtime Summary
                OvertimeSummary::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'overtime_jam' => $row['overtime_jam'] ?? null,
                        'lembur_hari' => $row['lembur_hari'] ?? null,
                        'lembur_jam' => $row['lembur_jam'] ?? null,
                        'lembur_jam_biasa' => $row['lembur_jam_biasa'] ?? null,
                        'lembur_jam_khusus' => $row['lembur_jam_khusus'] ?? null,
                        'lembur_minggu_2' => $row['lembur_minggu_2'] ?? null,
                        'lembur_minggu_3' => $row['lembur_minggu_3'] ?? null,
                        'lembur_minggu_4' => $row['lembur_minggu_4'] ?? null,
                        'lembur_minggu_5' => $row['lembur_minggu_5'] ?? null,
                        'lembur_minggu_6' => $row['lembur_minggu_6'] ?? null,
                        'lembur_minggu_7' => $row['lembur_minggu_7'] ?? null,
                        'lembur_libur' => $row['lembur_libur'] ?? null,
                        'lembur_2' => $row['lembur_2'] ?? null,
                    ]
                );

                // 7. Earnings
                Earning::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'gaji_hk' => $row['gaji_hk'] ?? null,
                        'gaji_hl' => $row['gaji_hl'] ?? null,
                        'gaji_hr' => $row['gaji_hr'] ?? null,
                        'gaji_jml' => $row['gaji_jml'] ?? null,
                        'gaji_train_jml' => $row['gaji_train_jml'] ?? null,
                        'gaji_rev' => $row['gaji_rev'] ?? null,
                        'gaji_lbh_tgl23_bulan_lalu' => $row['gaji_lbh_tgl23_bulan_lalu'] ?? null,
                        'lembur_jml' => $row['lembur_jml'] ?? null,
                        'lembur_jml_hk' => $row['lembur_jml_hk'] ?? null,
                        'lembur_jml_hl' => $row['lembur_jml_hl'] ?? null,
                        'lembur_jml_hr' => $row['lembur_jml_hr'] ?? null,
                        'lembur_biasa_jml' => $row['lembur_biasa_jml'] ?? null,
                        'lembur_khusus_jml' => $row['lembur_khusus_jml'] ?? null,
                        'lembur_kurang_bulan_lalu' => $row['lembur_kurang_bulan_lalu'] ?? null,
                        'overtime' => $row['overtime'] ?? null,
                        'fee_lembur' => $row['fee_lembur'] ?? null,
                    ]
                );

                // 8. Allowances
                Allowance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'tunj' => $row['tunj'] ?? null,
                        'tunj_sewa_motor' => $row['tunj_sewa_motor'] ?? null,
                        'tunj_bbm' => $row['tunj_bbm'] ?? null,
                        'tunj_pulsa' => $row['tunj_pulsa'] ?? null,
                        'tunj_penampilan' => $row['tunj_penampilan'] ?? null,
                        'tunj_shift' => $row['tunj_shift'] ?? null,
                        'tunj_makan' => $row['tunj_makan'] ?? null,
                        'tunj_transport' => $row['tunj_transport'] ?? null,
                        'tunj_kost' => $row['tunj_kost'] ?? null,
                        'tunj_maintenance' => $row['tunj_maintenance'] ?? null,
                        'tunj_posisi' => $row['tunj_posisi'] ?? null,
                        'tunj_fisik' => $row['tunj_fisik'] ?? null,
                        'tunj_loyalitas' => $row['tunj_loyalitas'] ?? null,
                        'tunj_operator' => $row['tunj_operator'] ?? null,
                        'tunj_jabatan' => $row['tunj_jabatan'] ?? null,
                        'tunj_bag' => $row['tunj_bag'] ?? null,
                    ]
                );

                // 9. Additional Earnings
                AdditionalEarning::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'anjem_jam' => $row['anjem_jam'] ?? null,
                        'anjem_hari' => $row['anjem_hari'] ?? null,
                        'anjem_jml' => $row['anjem_jml'] ?? null,
                        'borongan_kg' => $row['borongan_kg'] ?? null,
                        'borongan_jml' => $row['borongan_jml'] ?? null,
                        'retase' => $row['retase'] ?? null,
                        'retase_bongkar' => $row['retase_bongkar'] ?? null,
                        'piket_l_biasa' => $row['piket_l_biasa'] ?? null,
                        'piket_l_besar' => $row['piket_l_besar'] ?? null,
                        'piket_l_lain' => $row['piket_l_lain'] ?? null,
                        'piket_bbm' => $row['piket_bbm'] ?? null,
                        'piket_reguler' => $row['piket_reguler'] ?? null,
                        'piket_hari_raya' => $row['piket_hari_raya'] ?? null,
                        'upah_hr_nasional' => $row['upah_hr_nasional'] ?? null,
                        'upah_hr_raya' => $row['upah_hr_raya'] ?? null,
                        'lmbr_hr_nasional' => $row['lmbr_hr_nasional'] ?? null,
                        'bonus' => $row['bonus'] ?? null,
                        'premi' => $row['premi'] ?? null,
                        'insentif' => $row['insentif'] ?? null,
                        'insentif_malam' => $row['insentif_malam'] ?? null,
                        'perdin' => $row['perdin'] ?? null,
                        'pengiriman' => $row['pengiriman'] ?? null,
                        'uang_extra' => $row['uang_extra'] ?? null,
                        'accident' => $row['accident'] ?? null,
                        'pelatihan_gaji' => $row['pelatihan_gaji'] ?? null,
                        'rapelan' => $row['rapelan'] ?? null,
                        'kurang_bulan_lalu' => $row['kurang_bulan_lalu'] ?? null,
                        'koreksi_gaji_plus' => $row['koreksi_gaji_plus'] ?? null,
                        'koreksi_pph21' => $row['koreksi_pph21'] ?? null,
                        'pengembalian_pph21' => $row['pengembalian_pph21'] ?? null,
                        'pembulatan' => $row['pembulatan'] ?? null,
                        'lain_lain' => $row['lain_lain'] ?? null,
                    ]
                );

                // 10. Deductions
                Deduction::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'pot_makan' => $row['pot_makan'] ?? null,
                        'pot_bpjs_tk' => $row['pot_bpjs_tk'] ?? null,
                        'pot_bpjs_kes' => $row['pot_bpjs_kes'] ?? null,
                        'pot_bpjs' => $row['pot_bpjs'] ?? null,
                        'pot_koperasi' => $row['pot_koperasi'] ?? null,
                        'pot_bonus_gantung' => $row['pot_bonus_gantung'] ?? null,
                        'pot_jam_kerja' => $row['pot_jam_kerja'] ?? null,
                        'pot_materai' => $row['pot_materai'] ?? null,
                        'pot_kerusakan' => $row['pot_kerusakan'] ?? null,
                        'pot_admin' => $row['pot_admin'] ?? null,
                        'pot_apd' => $row['pot_apd'] ?? null,
                        'pot_alfa' => $row['pot_alfa'] ?? null,
                        'pot_jamsos' => $row['pot_jamsos'] ?? null,
                        'pot_sptp' => $row['pot_sptp'] ?? null,
                        'pot_payroll' => $row['pot_payroll'] ?? null,
                        'pot_seragam' => $row['pot_seragam'] ?? null,
                        'pot_tdk_masuk_jml' => $row['pot_tdk_masuk_jml'] ?? null,
                        'pot_tdk_finger' => $row['pot_tdk_finger'] ?? null,
                        'pot_pph21' => $row['pot_pph21'] ?? null,
                        'pot_hari_mingu' => $row['pot_hari_mingu'] ?? null,
                        'pot_lain' => $row['pot_lain'] ?? null,
                        'klaim' => $row['klaim'] ?? null,
                        'denda' => $row['denda'] ?? null,
                        'denda_telat_briefing' => $row['denda_telat_briefing'] ?? null,
                        'kas' => $row['kas'] ?? null,
                        'kasbon' => $row['kasbon'] ?? null,
                        'mangkir_jml' => $row['mangkir_jml'] ?? null,
                        'terlambat_jml' => $row['terlambat_jml'] ?? null,
                        'koreksi_gaji_minus' => $row['koreksi_gaji_minus'] ?? null,
                    ]
                );

                // 11. Payroll Summary
                PayrollSummary::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'no' => $row['no'] ?? null,
                        'grand_total' => $row['grand_total'] ?? null,
                        'exactsumef2ef10485761rincian_46ab761trainingn24' => $row['exactsumef2ef10485761rincian_46ab761trainingn24'] ?? null,
                    ]
                );

                DB::commit();
                
                $this->incrementSuccess();
                Log::info("Successfully imported NIK: {$nik}");

            } catch (\Exception $e) {
                DB::rollBack();
                
                $errorMessage = "Row " . ($index + 2) . " - NIK: " . ($nik ?? 'N/A') . " - Error: " . $e->getMessage();
                
                $this->incrementFailed($errorMessage);
                
                Log::error('Payroll Import Error', [
                    'row' => $index + 2,
                    'nik' => $nik ?? 'N/A',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                continue;
            }
        }
    }

    protected function incrementSuccess()
    {
        $key = "import_{$this->importId}_success";
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->addHours(24));
    }

    protected function incrementFailed($error)
    {
        // Increment failed count
        $keyFailed = "import_{$this->importId}_failed";
        $currentFailed = Cache::get($keyFailed, 0);
        Cache::put($keyFailed, $currentFailed + 1, now()->addHours(24));
        
        // Add error to list
        $keyErrors = "import_{$this->importId}_errors";
        $errors = Cache::get($keyErrors, []);
        $errors[] = $error;
        Cache::put($keyErrors, $errors, now()->addHours(24));
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getResults(): array
    {
        return [
            'import_id' => $this->importId,
            'success' => Cache::get("import_{$this->importId}_success", 0),
            'failed' => Cache::get("import_{$this->importId}_failed", 0),
            'errors' => Cache::get("import_{$this->importId}_errors", []),
            'total' => Cache::get("import_{$this->importId}_success", 0) + Cache::get("import_{$this->importId}_failed", 0),
        ];
    }

    // Method untuk check progress dari luar
    public static function getImportProgress($importId): array
    {
        return [
            'import_id' => $importId,
            'success' => Cache::get("import_{$importId}_success", 0),
            'failed' => Cache::get("import_{$importId}_failed", 0),
            'errors' => Cache::get("import_{$importId}_errors", []),
            'total' => Cache::get("import_{$importId}_success", 0) + Cache::get("import_{$importId}_failed", 0),
        ];
    }
}