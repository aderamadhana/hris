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
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PayslipImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts,
    WithCalculatedFormulas,
    ShouldQueue
{
    protected $payrollPeriodId;
    protected $errors = [];
    protected $successCount = 0;
    protected $failedCount = 0;

    public function __construct($payrollPeriodId)
    {
        $this->payrollPeriodId = $payrollPeriodId;
    }

    public function collection(Collection $rows)
    {
        Log::info($rows);
        foreach ($rows as $index => $row) {
            try {
                DB::beginTransaction();

                // 1. Cek NIK di employee_personals dan get employee_id
                $nik = (string) ((int) $row['nik']);
                $employeePersonal = EmployeePersonal::where('no_ktp', $nik)->first();
                
                if (!$employeePersonal) {
                    DB::rollBack();
                    Log::info("NIK {$nik} tidak ditemukan di employee_personals");
                    continue;
                    // throw new \Exception("NIK {$nik} tidak ditemukan di employee_personals");
                }

                $employeeId = $employeePersonal->employee_id;

                // 2. Validasi employee exists
                $employee = Employee::find($employeeId);
                if (!$employee) {
                    DB::rollBack();
                    Log::info("Employee ID {$employeeId} tidak ditemukan");
                    continue;
                }

                // 3. Update atau create employee data (jika ada perubahan)
                $employee->update([
                    'nik_kary' => $row['nik_kary'],
                    'no_rek' => $row['no_rek'],
                    'nama' => $row['nama'],
                    'status_kary' => $row['status_kary'],
                    'bagian' => $row['bagian'],
                    'area_kerja' => $row['area_kerja'],
                ]);

                // 4. Salary Configuration (optional, jika ada data)
                if ($row['gaji_pokok'] || $row['gaji_per_hari']) {
                    SalaryConfiguration::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'effective_date' => now()->startOfMonth(),
                        ],
                        [
                            'gaji_pokok' => $row['gaji_pokok'],
                            'gaji_per_hari' => $row['gaji_per_hari'],
                            'gaji_train_hk' => $row['gaji_train_hk'],
                            'gaji_train_upah_per_jam' => $row['gaji_train_upah_per_jam'],
                            'lembur_per_hari' => $row['lembur_per_hari'],
                            'lembur_per_jam' => $row['lembur_per_jam'],
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
                        'jam_kerja' => $row['jam_kerja'],
                        'jam_hk' => $row['jam_hk'],
                        'jam_hl' => $row['jam_hl'],
                        'jam_hr' => $row['jam_hr'],
                        'jml_hl' => $row['jml_hl'],
                        'jml_hr' => $row['jml_hr'],
                        'hadir' => $row['hadir'],
                        'mangkir_hari' => $row['mangkir_hari'],
                        'pot_tdk_masuk_hari' => $row['pot_tdk_masuk_hari'],
                        'terlambat_hari' => $row['terlambat_hari'],
                        'terlambat_menit' => $row['terlambat_menit'],
                        'terlambat_jam' => $row['terlambat_jam'],
                        'ijin_pulang' => $row['ijin_pulang'],
                        'cuti_dibayar' => $row['cuti_dibayar'],
                    ]
                );

                // 6. Overtime Summary
                OvertimeSummary::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'overtime_jam' => $row['overtime_jam'],
                        'lembur_hari' => $row['lembur_hari'],
                        'lembur_jam' => $row['lembur_jam'],
                        'lembur_jam_biasa' => $row['lembur_jam_biasa'],
                        'lembur_jam_khusus' => $row['lembur_jam_khusus'],
                        'lembur_minggu_2' => $row['lembur_minggu_2'],
                        'lembur_minggu_3' => $row['lembur_minggu_3'],
                        'lembur_minggu_4' => $row['lembur_minggu_4'],
                        'lembur_minggu_5' => $row['lembur_minggu_5'],
                        'lembur_minggu_6' => $row['lembur_minggu_6'],
                        'lembur_minggu_7' => $row['lembur_minggu_7'],
                        'lembur_libur' => $row['lembur_libur'],
                        'lembur_2' => $row['lembur_2'],
                    ]
                );

                // 7. Earnings
                Earning::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'gaji_hk' => $row['gaji_hk'],
                        'gaji_hl' => $row['gaji_hl'],
                        'gaji_hr' => $row['gaji_hr'],
                        'gaji_jml' => $row['gaji_jml'],
                        'gaji_train_jml' => $row['gaji_train_jml'],
                        'gaji_rev' => $row['gaji_rev'],
                        'gaji_lbh_tgl23_bulan_lalu' => $row['gaji_lbh_tgl23_bulan_lalu'],
                        'lembur_jml' => $row['lembur_jml'],
                        'lembur_jml_hk' => $row['lembur_jml_hk'],
                        'lembur_jml_hl' => $row['lembur_jml_hl'],
                        'lembur_jml_hr' => $row['lembur_jml_hr'],
                        'lembur_biasa_jml' => $row['lembur_biasa_jml'],
                        'lembur_khusus_jml' => $row['lembur_khusus_jml'],
                        'lembur_kurang_bulan_lalu' => $row['lembur_kurang_bulan_lalu'],
                        'overtime' => $row['overtime'],
                        'fee_lembur' => $row['fee_lembur'],
                    ]
                );

                // 8. Allowances
                Allowance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'tunj' => $row['tunj'],
                        'tunj_sewa_motor' => $row['tunj_sewa_motor'],
                        'tunj_bbm' => $row['tunj_bbm'],
                        'tunj_pulsa' => $row['tunj_pulsa'],
                        'tunj_penampilan' => $row['tunj_penampilan'],
                        'tunj_shift' => $row['tunj_shift'],
                        'tunj_makan' => $row['tunj_makan'],
                        'tunj_transport' => $row['tunj_transport'],
                        'tunj_kost' => $row['tunj_kost'],
                        'tunj_maintenance' => $row['tunj_maintenance'],
                        'tunj_posisi' => $row['tunj_posisi'],
                        'tunj_fisik' => $row['tunj_fisik'],
                        'tunj_loyalitas' => $row['tunj_loyalitas'],
                        'tunj_operator' => $row['tunj_operator'],
                        'tunj_jabatan' => $row['tunj_jabatan'],
                        'tunj_bag' => $row['tunj_bag'],
                    ]
                );

                // 9. Additional Earnings
                AdditionalEarning::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'anjem_jam' => $row['anjem_jam'],
                        'anjem_hari' => $row['anjem_hari'],
                        'anjem_jml' => $row['anjem_jml'],
                        'borongan_kg' => $row['borongan_kg'],
                        'borongan_jml' => $row['borongan_jml'],
                        'retase' => $row['retase'],
                        'retase_bongkar' => $row['retase_bongkar'],
                        'piket_l_biasa' => $row['piket_l_biasa'],
                        'piket_l_besar' => $row['piket_l_besar'],
                        'piket_l_lain' => $row['piket_l_lain'],
                        'piket_bbm' => $row['piket_bbm'],
                        'piket_reguler' => $row['piket_reguler'],
                        'piket_hari_raya' => $row['piket_hari_raya'],
                        'upah_hr_nasional' => $row['upah_hr_nasional'],
                        'upah_hr_raya' => $row['upah_hr_raya'],
                        'lmbr_hr_nasional' => $row['lmbr_hr_nasional'],
                        'bonus' => $row['bonus'],
                        'premi' => $row['premi'],
                        'insentif' => $row['insentif'],
                        'insentif_malam' => $row['insentif_malam'],
                        'perdin' => $row['perdin'],
                        'pengiriman' => $row['pengiriman'],
                        'uang_extra' => $row['uang_extra'],
                        'accident' => $row['accident'],
                        'pelatihan_gaji' => $row['pelatihan_gaji'],
                        'rapelan' => $row['rapelan'],
                        'kurang_bulan_lalu' => $row['kurang_bulan_lalu'],
                        'koreksi_gaji_plus' => $row['koreksi_gaji_plus'],
                        'koreksi_pph21' => $row['koreksi_pph21'],
                        'pengembalian_pph21' => $row['pengembalian_pph21'],
                        'pembulatan' => $row['pembulatan'],
                        'lain_lain' => $row['lain_lain'],
                    ]
                );

                // 10. Deductions
                Deduction::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'pot_makan' => $row['pot_makan'],
                        'pot_bpjs_tk' => $row['pot_bpjs_tk'],
                        'pot_bpjs_kes' => $row['pot_bpjs_kes'],
                        'pot_bpjs' => $row['pot_bpjs'],
                        'pot_koperasi' => $row['pot_koperasi'],
                        'pot_bonus_gantung' => $row['pot_bonus_gantung'],
                        'pot_jam_kerja' => $row['pot_jam_kerja'],
                        'pot_materai' => $row['pot_materai'],
                        'pot_kerusakan' => $row['pot_kerusakan'],
                        'pot_admin' => $row['pot_admin'],
                        'pot_apd' => $row['pot_apd'],
                        'pot_alfa' => $row['pot_alfa'],
                        'pot_jamsos' => $row['pot_jamsos'],
                        'pot_sptp' => $row['pot_sptp'],
                        'pot_payroll' => $row['pot_payroll'],
                        'pot_seragam' => $row['pot_seragam'],
                        'pot_tdk_masuk_jml' => $row['pot_tdk_masuk_jml'],
                        'pot_tdk_finger' => $row['pot_tdk_finger'],
                        'pot_pph21' => $row['pot_pph21'],
                        'pot_hari_mingu' => $row['pot_hari_mingu'],
                        'pot_lain' => $row['pot_lain'],
                        'klaim' => $row['klaim'],
                        'denda' => $row['denda'],
                        'denda_telat_briefing' => $row['denda_telat_briefing'],
                        'kas' => $row['kas'],
                        'kasbon' => $row['kasbon'],
                        'mangkir_jml' => $row['mangkir_jml'],
                        'terlambat_jml' => $row['terlambat_jml'],
                        'koreksi_gaji_minus' => $row['koreksi_gaji_minus'],
                    ]
                );

                // 11. Payroll Summary
                PayrollSummary::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'payroll_period_id' => $this->payrollPeriodId,
                    ],
                    [
                        'no' => $row['no'],
                        'grand_total' => $row['grand_total'],
                        'exactsumef2ef10485761rincian_46ab761trainingn24' => $row['exactsumef2ef10485761rincian_46ab761trainingn24'] ?? null,
                    ]
                );

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->failedCount++;
                
                $errorMessage = "Row " . ($index + 2) . " - NIK: " . ($nik ?? 'N/A') . " - Error: " . $e->getMessage();
                $this->errors[] = $errorMessage;
                
                Log::error('Payroll Import Error', [
                    'row' => $index + 2,
                    'nik' => $nik ?? 'N/A',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Continue to next row instead of stopping
                continue;
            }
        }
    }

    // Chunk size untuk memproses data dalam batch (performance optimization)
    public function chunkSize(): int
    {
        return 100;
    }

    // Batch insert untuk performance
    public function batchSize(): int
    {
        return 100;
    }

    // Get import results
    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failedCount,
            'errors' => $this->errors,
            'total' => $this->successCount + $this->failedCount,
        ];
    }
}