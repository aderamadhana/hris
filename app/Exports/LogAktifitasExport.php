<?php

namespace App\Exports;

use App\Models\LogAktifitas;
use App\Models\Divisi;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogAktifitasExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    private string $search;
    private ?int $jabatanId;
    private ?int $perusahaanId;
    private ?string $tanggalDari;
    private ?string $tanggalSampai;
    private int $no = 0;

    public function __construct(
        $search = '',
        $jabatanId = null,
        $perusahaanId = null,
        $tanggalDari = null,
        $tanggalSampai = null
    ) {
        $this->search = trim((string) $search);
        $this->jabatanId = $jabatanId;
        $this->perusahaanId = $perusahaanId;
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
    }

    public function query()
    {
        $query = LogAktifitas::query()
            ->with([
                'employee:id,nama',
                'employee.currentEmployment:id,employee_id,perusahaan,penempatan',
            ])
            ->orderByDesc('tgl')
            ->orderByDesc('created_at');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kode_kerja', 'like', "%{$this->search}%")
                  ->orWhereHas('employee', function ($qe) {
                      $qe->where('nama', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->tanggalDari && $this->tanggalSampai) {
            $query->whereBetween('tgl', [$this->tanggalDari, $this->tanggalSampai]);
        } elseif ($this->tanggalDari) {
            $query->whereDate('tgl', '>=', $this->tanggalDari);
        } elseif ($this->tanggalSampai) {
            $query->whereDate('tgl', '<=', $this->tanggalSampai);
        }

        if ($this->jabatanId) {
            $jabatanName = Divisi::where('id', $this->jabatanId)->value('nama_divisi');

            if ($jabatanName) {
                $query->whereHas('employee.currentEmployment', function ($q) use ($jabatanName) {
                    $q->where('penempatan', $jabatanName);
                });
            }
        }

        if ($this->perusahaanId) {
            $perusahaanName = Perusahaan::where('id', $this->perusahaanId)->value('nama_perusahaan');

            if ($perusahaanName) {
                $query->whereHas('employee.currentEmployment', function ($q) use ($perusahaanName) {
                    $q->where('perusahaan', $perusahaanName);
                });
            }
        }

        return $query;
    }

    public function map($row): array
    {
        $this->no++;

        $employment = $row->employee?->currentEmployment;

        return [
            $this->no,
            $row->reg,
            $row->employee->nama ?? '-',
            optional($row->tgl)->format('d-m-Y'),
            $row->shift,
            $row->bag,
            $row->lo,
            $row->jam_masuk ? Carbon::parse($row->jam_masuk)->format('H:i') : '',
            $row->jam_pulang ? Carbon::parse($row->jam_pulang)->format('H:i') : '',
            $row->jam_kerja_menit
                ? sprintf('%02d:%02d', intdiv($row->jam_kerja_menit, 60), $row->jam_kerja_menit % 60)
                : '',
            $row->kode_kerja,
            $row->hasil_kerja,
            $row->hasil_lembur,
            $row->return_qty,
            $row->tolak_qc,
            $row->upah_scf,
            $row->bantu_scf,
            $row->denda_scf,
            $row->total_scf,
            $row->upah_act,
            $row->upah_bantu_act,
            $row->return_act,
            $row->denda_act,
            $row->total_act,
            $row->ket,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Reg',
            'Nama',
            'Tanggal',
            'Shift',
            'Bag',
            'L/O',
            'Jam Masuk',
            'Jam Pulang',
            'Jam Kerja',
            'Kode Kerja',
            'Hasil Kerja',
            'Hasil Lembur',
            'Return',
            'Tolak QC',
            'Upah SCF',
            'Bantu SCF',
            'Denda SCF',
            'Total SCF',
            'Upah ACT',
            'Upah Bantu ACT',
            'Return ACT',
            'Denda ACT',
            'Total ACT',
            'Keterangan',
        ];
    }
}
