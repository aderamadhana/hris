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

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PayslipImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithChunkReading, 
    WithCalculatedFormulas,
    ShouldQueue
{
    protected $payrollPeriodId;
    protected $importId;
    protected $startTime;
    protected $maxExecutionTime = 540; // 9 menit

    public $timeout = 600; // 10 menit job timeout
    public $tries = 1;

    public function __construct($payrollPeriodId, $importId = null)
    {
        $this->payrollPeriodId = $payrollPeriodId;
        $this->importId = $importId ?? uniqid('import_', true);
        $this->startTime = time();
        
        // Initialize counters
        Cache::put("import_{$this->importId}_success", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_failed", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_errors", [], now()->addHours(24));
        Cache::put("import_{$this->importId}_status", 'processing', now()->addHours(24));
    }

    public function collection(Collection $rows)
    {
        DB::connection()->disableQueryLog();
        
        // Log::info("Starting import with {$rows->count()} rows for import ID: {$this->importId}");
        
        // Filter row yang benar-benar kosong
        $rows = $rows->filter(function ($row) {
            // Cek apakah row punya minimal 1 kolom yang terisi
            return collect($row)->filter(function ($value) {
                return $value !== null && $value !== '';
            })->isNotEmpty();
        });
        
        // Atau lebih spesifik: cek NIK harus ada
        $rows = $rows->filter(function ($row) {
            $nik = (string)((int)($row['nik'] ?? 0));
            return $nik !== '0' && $nik !== '';
        });
        
        if ($rows->isEmpty()) {
            Log::warning('No valid rows to import', ['import_id' => $this->importId]);
            $this->finalizeImport();
            return;
        }
        
        // Log::info("Starting import with {$rows->count()} valid rows for import ID: {$this->importId}");
        
        $niks = $rows->pluck('nik')->map(fn($nik) => (string)((int)$nik))->filter()->unique();
        $employeeMap = $this->preloadEmployees($niks);
        
        // Process dalam batch
        $chunks = $rows->chunk(50); // 50 rows per batch
        
        foreach ($chunks as $chunkIndex => $chunk) {
            if ($this->isTimeout()) {
                $this->handleTimeout();
                break;
            }
            
            Log::info("Processing chunk " . ($chunkIndex + 1) . " with {$chunk->count()} rows");
            
            DB::beginTransaction();
            try {
                foreach ($chunk as $index => $row) {
                    $this->processRow($row, $employeeMap, $index);
                }
                
                DB::commit();
                $this->incrementSuccess($chunk->count());
                
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Batch failed, processing individually", ['error' => $e->getMessage()]);
                
                // Process ulang satu per satu untuk error tracking
                foreach ($chunk as $index => $row) {
                    DB::beginTransaction();
                    try {
                        $this->processRow($row, $employeeMap, $index);
                        DB::commit();
                        $this->incrementSuccess();
                    } catch (\Throwable $ex) {
                        DB::rollBack();
                        $this->logError($row, $ex, $index);
                    }
                }
            }
        }
        
        $this->finalizeImport();
    }

    /**
     * Pre-load semua employee data untuk menghindari N+1 query
     */
    protected function preloadEmployees(Collection $niks): array
    {
        $employeePersonals = EmployeePersonal::whereIn('no_ktp', $niks)
            ->with('employee')
            ->get()
            ->keyBy('no_ktp');
        
        $map = [];
        foreach ($employeePersonals as $nik => $personal) {
            if ($personal->employee) {
                $map[$nik] = [
                    'personal' => $personal,
                    'employee' => $personal->employee,
                    'employee_id' => $personal->employee_id
                ];
            }
        }
        
        return $map;
    }

    /**
     * Process single row
     */
    protected function processRow($row, array $employeeMap, int $index)
    {
        $row = collect($row)->map(fn($val) => $this->sanitizeNumeric($val))->toArray();

        $nik = (string)((int)($row['nik'] ?? 0));
        
        if (empty($nik) || $nik === '0') {
            throw new \Exception("NIK kosong atau invalid pada row " . ($index + 2));
        }
        
        $hasData = collect($row)->filter(function ($value) {
            return $value !== null && $value !== '' && $value !== '0';
        })->isNotEmpty();
        
        if (!$hasData) {
            throw new \Exception("Row kosong pada row " . ($index + 2));
        }

        // Cek dari pre-loaded map
        if (!isset($employeeMap[$nik])) {
            throw new \Exception("NIK {$nik} tidak ditemukan di employee_personals");
        }
        
        $employeeData = $employeeMap[$nik];
        $employee = $employeeData['employee'];
        $employeeId = $employeeData['employee_id'];
        
        // Update Employee data
        $employee->update([
            'nik_kary' => $row['nik_kary'] ?? null,
            'no_rek' => $row['no_rek'] ?? null,
            // 'nama' => $row['nama'] ?? $employee->nama,
            // 'status_kary' => $row['status_kary'] ?? null,
            // 'bagian' => $row['bagian'] ?? null,
            // 'area_kerja' => $row['area_kerja'] ?? null,
        ]);
        
        // Salary Configuration (if exists)
       if (isset($row['gaji_pokok']) || isset($row['gaji_per_hari'])) {
            SalaryConfiguration::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'effective_date' => now()->startOfMonth(),
                ],
                [
                    'gaji_pokok' => $this->sanitizeNumeric($row['gaji_pokok'] ?? null),
                    'gaji_per_hari' => $this->sanitizeNumeric($row['gaji_per_hari'] ?? null),
                    'gaji_train_hk' => $this->sanitizeNumeric($row['gaji_train_hk'] ?? null),
                    'gaji_train_upah_per_jam' => $this->sanitizeNumeric($row['gaji_train_upah_per_jam'] ?? null),
                    'lembur_per_hari' => $this->sanitizeNumeric($row['lembur_per_hari'] ?? null),
                    'lembur_per_jam' => $this->sanitizeNumeric($row['lembur_per_jam'] ?? null),
                ]
            );
        }
        
        // Attendance Summary
        AttendanceSummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'jam_kerja', 'jam_hk', 'jam_hl', 'jam_hr', 
                'jml_hl', 'jml_hr', 'hadir', 'mangkir_hari',
                'pot_tdk_masuk_hari', 'terlambat_hari', 
                'terlambat_menit', 'terlambat_jam', 
                'ijin_pulang', 'cuti_dibayar'
            ])
        );
        
        // Overtime Summary
        OvertimeSummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'overtime_jam', 'lembur_hari', 'lembur_jam', 
                'lembur_jam_biasa', 'lembur_jam_khusus',
                'lembur_minggu_2', 'lembur_minggu_3', 
                'lembur_minggu_4', 'lembur_minggu_5',
                'lembur_minggu_6', 'lembur_minggu_7', 
                'lembur_libur', 'lembur_2'
            ])
        );
        
        // Earnings
        Earning::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'gaji_hk', 'gaji_hl', 'gaji_hr', 'gaji_jml',
                'gaji_train_jml', 'gaji_rev', 'gaji_lbh_tgl23_bulan_lalu',
                'lembur_jml', 'lembur_jml_hk', 'lembur_jml_hl',
                'lembur_jml_hr', 'lembur_biasa_jml', 'lembur_khusus_jml',
                'lembur_kurang_bulan_lalu', 'overtime', 'fee_lembur'
            ])
        );
        
        // Allowances - INI YANG ERROR!
        Allowance::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'tunj', 'tunj_sewa_motor', 'tunj_bbm', 'tunj_pulsa',
                'tunj_penampilan', 'tunj_shift', 'tunj_makan',
                'tunj_transport', 'tunj_kost', 'tunj_maintenance',
                'tunj_posisi', 'tunj_fisik', 'tunj_loyalitas',
                'tunj_operator', 'tunj_jabatan', 'tunj_bag'
            ])
        );
        
        // Additional Earnings
        AdditionalEarning::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'anjem_jam', 'anjem_hari', 'anjem_jml', 'borongan_kg',
                'borongan_jml', 'retase', 'retase_bongkar',
                'piket_l_biasa', 'piket_l_besar', 'piket_l_lain',
                'piket_bbm', 'piket_reguler', 'piket_hari_raya',
                'upah_hr_nasional', 'upah_hr_raya', 'lmbr_hr_nasional',
                'bonus', 'premi', 'insentif', 'insentif_malam',
                'perdin', 'pengiriman', 'uang_extra', 'accident',
                'pelatihan_gaji', 'rapelan', 'kurang_bulan_lalu',
                'koreksi_gaji_plus', 'koreksi_pph21', 'pengembalian_pph21',
                'pembulatan', 'lain_lain'
            ])
        );
        
        // Deductions
        Deduction::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
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
            ], [
                'pot_makan', 'pot_bpjs_tk', 'pot_bpjs_kes', 'pot_bpjs',
                'pot_koperasi', 'pot_bonus_gantung', 'pot_jam_kerja',
                'pot_materai', 'pot_kerusakan', 'pot_admin', 'pot_apd',
                'pot_alfa', 'pot_jamsos', 'pot_sptp', 'pot_payroll',
                'pot_seragam', 'pot_tdk_masuk_jml', 'pot_tdk_finger',
                'pot_pph21', 'pot_hari_mingu', 'pot_lain', 'klaim',
                'denda', 'denda_telat_briefing', 'kas', 'kasbon',
                'mangkir_jml', 'terlambat_jml', 'koreksi_gaji_minus'
            ])
        );
        
        // Payroll Summary
        PayrollSummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            $this->sanitizeNumericArray([
                'no' => $row['no'] ?? null,
                'grand_total' => $row['grand_total'] ?? null,
                'exactsumef2ef10485761rincian_46ab761trainingn24' => $row['exactsumef2ef10485761rincian_46ab761trainingn24'] ?? null,
            ], [
                'no', 'grand_total', 'exactsumef2ef10485761rincian_46ab761trainingn24'
            ])
        );
    }

    /**
     * Log error untuk specific row
     */
    protected function logError($row, \Throwable $e, int $index)
    {
        $nik = (string)((int)($row['nik'] ?? 0));
        $errorMessage = "Row " . ($index + 2) . " - NIK: {$nik} - Error: " . $e->getMessage();
        
        $this->incrementFailed($errorMessage);
        
        Log::error('Payroll Import Error', [
            'row' => $index + 2,
            'nik' => $nik,
            'error' => $e->getMessage(),
            'import_id' => $this->importId
        ]);
    }

    /**
     * Check timeout
     */
    protected function isTimeout(): bool
    {
        $elapsed = time() - $this->startTime;
        return $elapsed >= $this->maxExecutionTime;
    }

    /**
     * Handle timeout scenario
     */
    protected function handleTimeout()
    {
        Log::warning('Import timeout', ['import_id' => $this->importId]);
        
        Cache::put("import_{$this->importId}_status", 'timeout', now()->addHours(24));
        
        $errors = Cache::get("import_{$this->importId}_errors", []);
        $errors[] = "Import dihentikan karena timeout. Sebagian data berhasil diimport.";
        Cache::put("import_{$this->importId}_errors", $errors, now()->addHours(24));
    }

    /**
     * Finalize import
     */
    protected function finalizeImport()
    {
        $status = Cache::get("import_{$this->importId}_status", 'processing');
        
        if ($status !== 'timeout') {
            Cache::put("import_{$this->importId}_status", 'completed', now()->addHours(24));
        }
        
        Log::info('Import finished', [
            'import_id' => $this->importId,
            'status' => $status,
            'results' => $this->getResults()
        ]);
    }

    /**
     * Increment success counter
     */
    protected function incrementSuccess(int $count = 1)
    {
        $key = "import_{$this->importId}_success";
        $current = Cache::get($key, 0);
        Cache::put($key, $current + $count, now()->addHours(24));
    }

    /**
     * Increment failed counter and add error
     */
    protected function incrementFailed(string $error)
    {
        $keyFailed = "import_{$this->importId}_failed";
        $currentFailed = Cache::get($keyFailed, 0);
        Cache::put($keyFailed, $currentFailed + 1, now()->addHours(24));
        
        $keyErrors = "import_{$this->importId}_errors";
        $errors = Cache::get($keyErrors, []);
        $errors[] = $error;
        Cache::put($keyErrors, $errors, now()->addHours(24));
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Cache::put("import_{$this->importId}_status", 'failed', now()->addHours(24));
        
        $errors = Cache::get("import_{$this->importId}_errors", []);
        $errors[] = "Job failed: " . $exception->getMessage();
        Cache::put("import_{$this->importId}_errors", $errors, now()->addHours(24));
        
        Log::error('Payroll import job failed', [
            'import_id' => $this->importId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    public function chunkSize(): int
    {
        return 500; // Lebih besar untuk efisiensi queue
    }

    public function getResults(): array
    {
        return [
            'import_id' => $this->importId,
            'status' => Cache::get("import_{$this->importId}_status", 'unknown'),
            'success' => Cache::get("import_{$this->importId}_success", 0),
            'failed' => Cache::get("import_{$this->importId}_failed", 0),
            'errors' => Cache::get("import_{$this->importId}_errors", []),
            'total' => Cache::get("import_{$this->importId}_success", 0) + Cache::get("import_{$this->importId}_failed", 0),
        ];
    }

    public static function getImportProgress($importId): array
    {
        return [
            'import_id' => $importId,
            'status' => Cache::get("import_{$importId}_status", 'unknown'),
            'success' => Cache::get("import_{$importId}_success", 0),
            'failed' => Cache::get("import_{$importId}_failed", 0),
            'errors' => Cache::get("import_{$importId}_errors", []),
            'total' => Cache::get("import_{$importId}_success", 0) + Cache::get("import_{$importId}_failed", 0),
        ];
    }

    protected function sanitizeNumeric($value)
{
    if ($value === '' || $value === null || $value === '-') {
        return null;
    }
    
    // Remove non-numeric characters except dot and minus
    $cleaned = preg_replace('/[^0-9.-]/', '', $value);
    
    return $cleaned === '' ? null : $cleaned;
}

/**
 * Sanitize array of numeric values
 */
protected function sanitizeNumericArray(array $data, array $keys): array
{
    foreach ($keys as $key) {
        if (isset($data[$key])) {
            $data[$key] = $this->sanitizeNumeric($data[$key]);
        }
    }
    return $data;
}
}