<?php

namespace App\Imports;

use App\Models\Employee;
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

        Cache::put("import_{$this->importId}_success", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_failed", 0, now()->addHours(24));
        Cache::put("import_{$this->importId}_errors", [], now()->addHours(24));
        Cache::put("import_{$this->importId}_status", 'processing', now()->addHours(24));
    }

    public function collection(Collection $rows)
    {
        DB::connection()->disableQueryLog();

        // 1) buang row benar-benar kosong
        $rows = $rows->filter(function ($row) {
            return collect($row)->filter(function ($value) {
                return $value !== null && $value !== '';
            })->isNotEmpty();
        });

        // 2) pastikan NIK ada (jangan cast int)
        $rows = $rows->filter(function ($row) {
            $nik = $this->sanitizeNik($row['nik'] ?? '');
            return $nik !== '';
        });

        if ($rows->isEmpty()) {
            Log::warning('No valid rows to import', ['import_id' => $this->importId]);
            $this->finalizeImport();
            return;
        }

        $niks = $rows->pluck('nik')
            ->map(fn ($nik) => $this->sanitizeNik($nik))
            ->filter()
            ->unique();

        $employeeMap = $this->preloadEmployees($niks);

        // Process dalam batch kecil supaya transaksi lebih aman
        $chunks = $rows->chunk(50);

        foreach ($chunks as $chunkIndex => $chunk) {
            if ($this->isTimeout()) {
                $this->handleTimeout();
                break;
            }

            DB::beginTransaction();
            try {
                foreach ($chunk as $index => $row) {
                    $this->processRow($row, $employeeMap, $index);
                }

                DB::commit();
                $this->incrementSuccess($chunk->count());
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Batch failed, processing individually", [
                    'error' => $e->getMessage(),
                    'import_id' => $this->importId,
                ]);

                // ulang satu per satu untuk tracking error yang akurat
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
     * Preload employee berdasarkan no_ktp (karena personals sudah digabung ke employees)
     */
    protected function preloadEmployees(Collection $niks): array
    {
        $employees = Employee::whereIn('no_ktp', $niks)->get();

        $map = [];
        foreach ($employees as $employee) {
            $key = $this->sanitizeNik($employee->no_ktp ?? '');
            if ($key !== '') {
                $map[$key] = [
                    'employee' => $employee,
                    'employee_id' => $employee->id,
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
        $nik = $this->sanitizeNik($row['nik'] ?? '');

        if ($nik === '') {
            throw new \Exception("NIK kosong atau invalid pada row " . ($index + 2));
        }

        $hasData = collect($row)->filter(function ($value) {
            return $value !== null && $value !== '';
        })->isNotEmpty();

        if (!$hasData) {
            throw new \Exception("Row kosong pada row " . ($index + 2));
        }

        if (!isset($employeeMap[$nik])) {
            throw new \Exception("NIK {$nik} tidak ditemukan di employees.no_ktp");
        }

        $employee = $employeeMap[$nik]['employee'];
        $employeeId = $employeeMap[$nik]['employee_id'];

        /**
         * Update data employee sesuai migration baru:
         * - nik_kary -> nrp (jika file excel memang isinya NRP)
         * - no_rek   -> no_rekening
         */
        $employeeUpdate = [];

        if (isset($row['nik_kary']) && $row['nik_kary'] !== '') {
            $employeeUpdate['nrp'] = (string) $row['nik_kary'];
        }
        if (isset($row['no_rek']) && $row['no_rek'] !== '') {
            $employeeUpdate['no_rekening'] = (string) $row['no_rek'];
        }

        if (!empty($employeeUpdate)) {
            $employee->update($employeeUpdate);
        }

        // 1. Salary Configuration (numeric)
        if (
            isset($row['gaji_pokok']) || isset($row['gaji_per_hari']) ||
            isset($row['gaji_hk']) || isset($row['gaji_train_hk']) ||
            isset($row['gaji_train_upah_per_jam']) || isset($row['lembur_per_hari']) ||
            isset($row['lembur_per_jam'])
        ) {
            SalaryConfiguration::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'effective_date' => now()->startOfMonth(),
                ],
                [
                    'gaji_hk' => $this->sanitizeNumeric($row['gaji_hk'] ?? null),
                    'gaji_pokok' => $this->sanitizeNumeric($row['gaji_pokok'] ?? null),
                    'gaji_per_hari' => $this->sanitizeNumeric($row['gaji_per_hari'] ?? null),
                    'gaji_train_hk' => $this->sanitizeNumeric($row['gaji_train_hk'] ?? null),
                    'gaji_train_upah_per_jam' => $this->sanitizeNumeric($row['gaji_train_upah_per_jam'] ?? null),
                    'lembur_per_hari' => $this->sanitizeNumeric($row['lembur_per_hari'] ?? null),
                    'lembur_per_jam' => $this->sanitizeNumeric($row['lembur_per_jam'] ?? null),
                ]
            );
        }

        // 2. Attendance Summary (sebagian numeric, jam_kerja bisa integer atau format lain)
        $attendanceData = [
            'jam_kerja' => $row['jam_kerja'] ?? null, // integer
            'jam_hk' => $row['jam_hk'] ?? null,
            'jam_hl' => $row['jam_hl'] ?? null,
            'jam_hr' => $row['jam_hr'] ?? null,
            'jml_hl' => $row['jml_hl'] ?? null,
            'jml_hr' => $row['jml_hr'] ?? null,
            'hadir' => $row['hadir'] ?? null,
            'mangkir_hari' => $row['mangkir_hari'] ?? null,
            'pot_tdk_masuk_hari' => $row['pot_tdk_masuk_hari'] ?? null,
            'pot_tdk_masuk_upah' => $row['pot_tdk_masuk_upah'] ?? null,
            'terlambat_hari' => $row['terlambat_hari'] ?? null,
            'terlambat_menit' => $row['terlambat_menit'] ?? null,
            'terlambat_jam' => $row['terlambat_jam'] ?? null,
            'ijin_pulang' => $row['ijin_pulang'] ?? null,
            'cuti_dibayar' => $row['cuti_dibayar'] ?? null,
        ];

        // jam_kerja, jml_hl, jml_hr, hadir, mangkir_hari, pot_tdk_masuk_hari, terlambat_hari, terlambat_menit, ijin_pulang, cuti_dibayar = integer
        // jam_hk, jam_hl, jam_hr, terlambat_jam, pot_tdk_masuk_upah = decimal
        $attendanceData = $this->sanitizeNumericArray($attendanceData, [
            'jam_kerja', 'jam_hk', 'jam_hl', 'jam_hr', 'jml_hl', 'jml_hr', 'hadir', 'mangkir_hari',
            'pot_tdk_masuk_hari', 'pot_tdk_masuk_upah', 'terlambat_hari', 'terlambat_menit',
            'terlambat_jam', 'ijin_pulang', 'cuti_dibayar',
        ]);

        if (collect($attendanceData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            AttendanceSummary::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $attendanceData
            );
        }

        // 3. Overtime Summary (numeric)
        $overtimeData = [
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
        ];

        $overtimeData = $this->sanitizeNumericArray($overtimeData, array_keys($overtimeData));

        if (collect($overtimeData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            OvertimeSummary::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $overtimeData
            );
        }

        // 4. Earnings (numeric)
        $earningsData = [
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
        ];

        $earningsData = $this->sanitizeNumericArray($earningsData, array_keys($earningsData));

        if (collect($earningsData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            Earning::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $earningsData
            );
        }

        // 5. Allowances (numeric)
        $allowancesData = [
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
        ];

        $allowancesData = $this->sanitizeNumericArray($allowancesData, array_keys($allowancesData));

        if (collect($allowancesData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            Allowance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $allowancesData
            );
        }

        // 6. Additional Earnings (numeric)
        $additionalEarningsData = [
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
        ];

        $additionalEarningsData = $this->sanitizeNumericArray($additionalEarningsData, array_keys($additionalEarningsData));

        if (collect($additionalEarningsData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            AdditionalEarning::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $additionalEarningsData
            );
        }

        // 7. Deductions (numeric)
        $deductionsData = [
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
        ];

        $deductionsData = $this->sanitizeNumericArray($deductionsData, array_keys($deductionsData));

        if (collect($deductionsData)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            Deduction::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_period_id' => $this->payrollPeriodId,
                ],
                $deductionsData
            );
        }

        // 8. Payroll Summary (selalu dibuat)
        PayrollSummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'payroll_period_id' => $this->payrollPeriodId,
            ],
            [
                'no' => $row['no'] ?? null,
                'grand_total' => $this->sanitizeNumeric($row['grand_total'] ?? 0) ?? 0,
                'exactsumef2ef10485761rincian_46ab761trainingn24' => $row['exactsumef2ef10485761rincian_46ab761trainingn24'] ?? null,
            ]
        );
    }

    /**
     * Log error untuk specific row
     */
    protected function logError($row, \Throwable $e, int $index)
    {
        $nik = $this->sanitizeNik($row['nik'] ?? '');
        $errorMessage = "Row " . ($index + 2) . " - NIK: {$nik} - Error: " . $e->getMessage();

        $this->incrementFailed($errorMessage);

        Log::error('Payroll Import Error', [
            'row' => $index + 2,
            'nik' => $nik,
            'error' => $e->getMessage(),
            'import_id' => $this->importId
        ]);
    }

    protected function isTimeout(): bool
    {
        $elapsed = time() - $this->startTime;
        return $elapsed >= $this->maxExecutionTime;
    }

    protected function handleTimeout()
    {
        Log::warning('Import timeout', ['import_id' => $this->importId]);

        Cache::put("import_{$this->importId}_status", 'timeout', now()->addHours(24));

        $errors = Cache::get("import_{$this->importId}_errors", []);
        $errors[] = "Import dihentikan karena timeout. Sebagian data berhasil diimport.";
        Cache::put("import_{$this->importId}_errors", $errors, now()->addHours(24));
    }

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

    protected function incrementSuccess(int $count = 1)
    {
        $key = "import_{$this->importId}_success";
        $current = Cache::get($key, 0);
        Cache::put($key, $current + $count, now()->addHours(24));
    }

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
        return 500;
    }

    public function getResults(): array
    {
        $success = Cache::get("import_{$this->importId}_success", 0);
        $failed = Cache::get("import_{$this->importId}_failed", 0);

        return [
            'import_id' => $this->importId,
            'status' => Cache::get("import_{$this->importId}_status", 'unknown'),
            'success' => $success,
            'failed' => $failed,
            'errors' => Cache::get("import_{$this->importId}_errors", []),
            'total' => $success + $failed,
        ];
    }

    public static function getImportProgress($importId): array
    {
        $success = Cache::get("import_{$importId}_success", 0);
        $failed = Cache::get("import_{$importId}_failed", 0);

        return [
            'import_id' => $importId,
            'status' => Cache::get("import_{$importId}_status", 'unknown'),
            'success' => $success,
            'failed' => $failed,
            'errors' => Cache::get("import_{$importId}_errors", []),
            'total' => $success + $failed,
        ];
    }

    /**
     * NIK: ambil digit saja (aman untuk 16 digit, tidak cast int)
     */
    private function sanitizeNik($value): string
    {
        $cleaned = preg_replace('/\D+/', '', trim((string) $value));
        
        if ($cleaned === '') {
            return '';
        }
        
        // Validasi: NIK harus antara 13-16 digit (toleransi)
        $length = strlen($cleaned);
        if ($length < 13 || $length > 16) {
            // Log warning untuk NIK yang tidak wajar
            Log::warning("NIK dengan panjang tidak standar: {$cleaned} ({$length} digit)");
        }
        
        // Pad ke 16 digit jika kurang
        if ($length < 16) {
            $cleaned = str_pad($cleaned, 16, '0', STR_PAD_LEFT);
        }
        
        return $cleaned;
    }

    /**
     * Numeric sanitizer:
     * - NULL / '' => null
     * - '0' => 0 (JANGAN jadi null)
     * - buang karakter non-numeric (kecuali . dan -)
     */
    private function sanitizeNumeric($value)
    {
        if ($value === null) {
            return null;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        // Keep digits, dot, minus
        $cleaned = preg_replace('/[^0-9.\-]/', '', $str);

        // Handle cases like "-" or "." after cleaning
        if ($cleaned === '' || $cleaned === '-' || $cleaned === '.' || $cleaned === '-.') {
            return null;
        }

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    /**
     * Sanitize array of numeric values (hanya keys tertentu)
     */
    protected function sanitizeNumericArray(array $data, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $this->sanitizeNumeric($data[$key]);
            }
        }
        return $data;
    }
}
