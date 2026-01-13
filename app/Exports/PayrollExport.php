<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Carbon\Carbon;

class PayrollExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize
{
    private int $rowNumber = 0;

    public function __construct(
        protected ?int $periode_id,
    ) {}

    public function query()
    {
        return Employee::query()
            ->with([
                'latestSalaryConfiguration',
                'attendanceSummaries' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'overtimeSummaries' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'earnings' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'allowances' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'additionalEarnings' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'deductions' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                },
                'payrollSummaries' => function ($query) {
                    $query->where('payroll_period_id', $this->periode_id);
                }
            ])
            ->whereHas('payrollSummaries', function ($query) {
                $query->where('payroll_period_id', $this->periode_id);
            })
            ->orderBy('id');
    }

    public function map($employee): array
    {
        $this->rowNumber++;
        
        // Get related data
        $salary = $employee->latestSalaryConfiguration;
        $attendance = $employee->attendanceSummaries->first();
        $overtime = $employee->overtimeSummaries->first();
        $earnings = $employee->earnings->first();
        $allowances = $employee->allowances->first();
        $additional = $employee->additionalEarnings->first();
        $deductions = $employee->deductions->first();
        $summary = $employee->payrollSummaries->first();

        return [
            $this->rowNumber, // no
            $employee->no_ktp ?? '',
            $employee->no_ktp ?? '',
            $employee->no_rekening ?? '',
            $employee->nama ?? '',
            $employee->status_kary ?? '',
            $employee->bagian ?? '',
            $employee->area_kerja ?? '',
            $earnings->gaji_hk ?? 0,
            $earnings->gaji_per_hari ?? 0,
            $earnings->gaji_train_hk ?? 0,
            $earnings->gaji_train_upah_per_jam ?? 0,
            $additional->anjem_jam ?? 0,
            $additional->anjem_hari ?? 0,
            $additional->borongan_kg ?? 0,
            $attendance->mangkir_hari ?? 0,
            $attendance->pot_tdk_masuk_hari ?? 0,
            $attendance->pot_tdk_masuk_upah ?? 0,
            $attendance->terlambat_hari ?? 0,
            $attendance->terlambat_menit ?? 0,
            $attendance->terlambat_jam ?? 0,
            $overtime->overtime_jam ?? 0,
            $overtime->lembur_hari ?? 0,
            $overtime->lembur_jam ?? 0,
            $salary->lembur_per_hari ?? 0,
            $salary->lembur_per_jam ?? 0,
            $overtime->lembur_jam_biasa ?? 0,
            $overtime->lembur_jam_khusus ?? 0,
            $earnings->gaji_hl ?? 0,
            $earnings->gaji_hr ?? 0,
            $attendance->jam_hk ?? 0,
            $attendance->jam_hl ?? 0,
            $attendance->jam_hr ?? 0,
            $salary->gaji_pokok ?? 0,
            $earnings->gaji_jml ?? 0,
            $earnings->gaji_train_jml ?? 0,
            $earnings->gaji_lbh_tgl23_bulan_lalu ?? 0,
            $additional->retase ?? 0,
            $additional->retase_bongkar ?? 0,
            $additional->anjem_jml ?? 0,
            $additional->borongan_jml ?? 0,
            $additional->upah_hr_nasional ?? 0,
            $additional->lmbr_hr_nasional ?? 0,
            $allowances->tunj ?? 0,
            $allowances->tunj_sewa_motor ?? 0,
            $allowances->tunj_bbm ?? 0,
            $allowances->tunj_pulsa ?? 0,
            $allowances->tunj_penampilan ?? 0,
            $allowances->tunj_shift ?? 0,
            $allowances->tunj_makan ?? 0,
            $allowances->tunj_transport ?? 0,
            $allowances->tunj_kost ?? 0,
            $allowances->tunj_maintenance ?? 0,
            $allowances->tunj_posisi ?? 0,
            $allowances->tunj_fisik ?? 0,
            $allowances->tunj_loyalitas ?? 0,
            $allowances->tunj_operator ?? 0,
            $allowances->tunj_jabatan ?? 0,
            $allowances->tunj_bag ?? 0,
            $additional->piket_l_biasa ?? 0,
            $additional->piket_l_besar ?? 0,
            $additional->piket_l_lain ?? 0,
            $attendance->jam_kerja ?? 0,
            $additional->piket_bbm ?? 0,
            $additional->bonus ?? 0,
            $additional->premi ?? 0,
            $additional->insentif ?? 0,
            $additional->perdin ?? 0,
            $additional->pengiriman ?? 0,
            $attendance->hadir ?? 0,
            $additional->insentif_malam ?? 0,
            $additional->uang_extra ?? 0,
            $earnings->fee_lembur ?? 0,
            $additional->upah_hr_raya ?? 0,
            $additional->kurang_bulan_lalu ?? 0,
            $additional->pengembalian_pph21 ?? 0,
            $additional->piket_reguler ?? 0,
            $additional->piket_hari_raya ?? 0,
            $additional->accident ?? 0,
            $attendance->jml_hl ?? 0,
            $attendance->jml_hr ?? 0,
            $earnings->lembur_jml_hk ?? 0,
            $earnings->lembur_jml_hl ?? 0,
            $earnings->lembur_jml_hr ?? 0,
            $additional->koreksi_gaji_plus ?? 0,
            $additional->koreksi_pph21 ?? 0,
            $earnings->lembur_jml ?? 0,
            $earnings->lembur_biasa_jml ?? 0,
            $earnings->lembur_khusus_jml ?? 0,
            $overtime->lembur_minggu_2 ?? 0,
            $overtime->lembur_minggu_3 ?? 0,
            $overtime->lembur_minggu_4 ?? 0,
            $overtime->lembur_minggu_5 ?? 0,
            $overtime->lembur_minggu_6 ?? 0,
            $overtime->lembur_minggu_7 ?? 0,
            $earnings->lembur_kurang_bulan_lalu ?? 0,
            $overtime->lembur_2 ?? 0,
            $overtime->lembur_libur ?? 0,
            $earnings->overtime ?? 0,
            $earnings->gaji_rev ?? 0,
            $attendance->cuti_dibayar ?? 0,
            $additional->pelatihan_gaji ?? 0,
            $additional->pembulatan ?? 0,
            $additional->lain_lain ?? 0,
            $deductions->pot_makan ?? 0,
            $deductions->pot_bpjs_tk ?? 0,
            $deductions->pot_bpjs_kes ?? 0,
            $deductions->pot_bpjs ?? 0,
            $deductions->pot_koperasi ?? 0,
            $deductions->pot_bonus_gantung ?? 0,
            $deductions->pot_jam_kerja ?? 0,
            $deductions->pot_materai ?? 0,
            $deductions->pot_kerusakan ?? 0,
            $deductions->pot_admin ?? 0,
            $deductions->pot_apd ?? 0,
            $deductions->klaim ?? 0,
            $deductions->denda ?? 0,
            $deductions->pot_alfa ?? 0,
            $deductions->pot_jamsos ?? 0,
            $deductions->pot_sptp ?? 0,
            $deductions->kas ?? 0,
            $deductions->kasbon ?? 0,
            $deductions->pot_payroll ?? 0,
            $deductions->pot_seragam ?? 0,
            $deductions->pot_lain ?? 0,
            $deductions->mangkir_jml ?? 0,
            $deductions->pot_tdk_masuk_jml ?? 0,
            $deductions->terlambat_jml ?? 0,
            $attendance->ijin_pulang ?? 0,
            $deductions->pot_tdk_finger ?? 0,
            $additional->rapelan ?? 0,
            $deductions->koreksi_gaji_minus ?? 0,
            $deductions->pot_pph21 ?? 0,
            $deductions->denda_telat_briefing ?? 0,
            $deductions->pot_hari_mingu ?? 0,
            $summary->grand_total ?? 0,
            // 'FALSE'
            null
        ];
    }

    public function headings(): array
    {
        return [
            'no',
            'nik',
            'nik_kary',
            'no_rek',
            'nama',
            'status_kary',
            'bagian',
            'area_kerja',
            'gaji_hk',
            'gaji_per_hari',
            'gaji_train_hk',
            'gaji_train_upah_per_jam',
            'anjem_jam',
            'anjem_hari',
            'borongan_kg',
            'mangkir_hari',
            'pot_tdk_masuk_hari',
            'pot_tdk_masuk_upah',
            'terlambat_hari',
            'terlambat_menit',
            'terlambat_jam',
            'overtime_jam',
            'lembur_hari',
            'lembur_jam',
            'lembur_per_hari',
            'lembur_per_jam',
            'lembur_jam_biasa',
            'lembur_jam_khusus',
            'gaji_hl',
            'gaji_hr',
            'jam_hk',
            'jam_hl',
            'jam_hr',
            'gaji_pokok',
            'gaji_jml',
            'gaji_train_jml',
            'gaji_lbh_tgl23_bulan_lalu',
            'retase',
            'retase_bongkar',
            'anjem_jml',
            'borongan_jml',
            'upah_hr_nasional',
            'lmbr_hr_nasional',
            'tunj',
            'tunj_sewa_motor',
            'tunj_bbm',
            'tunj_pulsa',
            'tunj_penampilan',
            'tunj_shift',
            'tunj_makan',
            'tunj_transport',
            'tunj_kost',
            'tunj_maintenance',
            'tunj_posisi',
            'tunj_fisik',
            'tunj_loyalitas',
            'tunj_operator',
            'tunj_jabatan',
            'tunj_bag',
            'piket_l_biasa',
            'piket_l_besar',
            'piket_l_lain',
            'jam_kerja',
            'piket_bbm',
            'bonus',
            'premi',
            'insentif',
            'perdin',
            'pengiriman',
            'hadir',
            'insentif_malam',
            'uang_extra',
            'fee_lembur',
            'upah_hr_raya',
            'kurang_bulan_lalu',
            'pengembalian_pph21',
            'piket_reguler',
            'piket_hari_raya',
            'accident',
            'jml_hl',
            'jml_hr',
            'lembur_jml_hk',
            'lembur_jml_hl',
            'lembur_jml_hr',
            'koreksi_gaji_plus',
            'koreksi_pph21',
            'lembur_jml',
            'lembur_biasa_jml',
            'lembur_khusus_jml',
            'lembur_minggu_2',
            'lembur_minggu_3',
            'lembur_minggu_4',
            'lembur_minggu_5',
            'lembur_minggu_6',
            'lembur_minggu_7',
            'lembur_kurang_bulan_lalu',
            'lembur_2',
            'lembur_libur',
            'overtime',
            'gaji_rev',
            'cuti_dibayar',
            'pelatihan_gaji',
            'pembulatan',
            'lain_lain',
            'pot_makan',
            'pot_bpjs_tk',
            'pot_bpjs_kes',
            'pot_bpjs',
            'pot_koperasi',
            'pot_bonus_gantung',
            'pot_jam_kerja',
            'pot_materai',
            'pot_kerusakan',
            'pot_admin',
            'pot_apd',
            'klaim',
            'denda',
            'pot_alfa',
            'pot_jamsos',
            'pot_sptp',
            'kas',
            'kasbon',
            'pot_payroll',
            'pot_seragam',
            'pot_lain',
            'mangkir_jml',
            'pot_tdk_masuk_jml',
            'terlambat_jml',
            'ijin_pulang',
            'pot_tdk_finger',
            'rapelan',
            'koreksi_gaji_minus',
            'pot_pph21',
            'denda_telat_briefing',
            'pot_hari_mingu',
            'grand_total',
            'FALSE'
        ];
    }
}