<?php

namespace App\Exports;

use App\Models\RekapPresensiHarian;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;

class PresensiExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize
{
    private int $rowNumber = 0;
    private $search;
    private $filtered_perusahaan;
    private $filtered_jabatan;
    private $filtered_tanggal_absen;

    public function __construct($search, $filtered_perusahaan, $filtered_jabatan, $filtered_tanggal_absen)
    {
        $this->search = $search;
        $this->filtered_perusahaan = $filtered_perusahaan;
        $this->filtered_jabatan = $filtered_jabatan;
        $this->filtered_tanggal_absen = $filtered_tanggal_absen;
    }

    public function bindValue(Cell $cell, $value)
    {
        // Force NIK to be treated as string to preserve leading zeros
        if ($cell->getColumn() === 'D' && is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function query()
    {
        $query = RekapPresensiHarian::with([
                'employee' => function($query) {
                    $query->select('id', 'nama', 'no_ktp');
                },
                'perusahaan' => function($query) {
                    $query->select('id', 'nama_perusahaan');
                }
            ])
            // ->where('perusahaan_id', $this->filtered_perusahaan)
            ->orderBy('tanggal', 'desc');

        // Filter by search
        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('no_ktp', 'like', '%' . $this->search . '%');
            });
        }

        // filter perusahaan (berdasarkan ID dari tabel perusahaan)
        if ($this->filtered_perusahaan) {
            $perusahaanId = (int) $this->filtered_perusahaan;
            $query->where('perusahaan_id', $perusahaanId);
        }

        // filter divisi/jabatan (berdasarkan ID dari tabel divisi)
        if ($this->filtered_jabatan) {
            $divisiId = (int) $this->filtered_jabatan;
            $query->where('divisi_id', $divisiId);
        }

        // Filter by tanggal
        if ($this->filtered_tanggal_absen) {
            $dates = explode(' to ', $this->filtered_tanggal_absen);
            if (count($dates) === 2) {
                $query->whereBetween('tanggal', [
                    Carbon::parse($dates[0])->format('Y-m-d'),
                    Carbon::parse($dates[1])->format('Y-m-d')
                ]);
            } else {
                $query->whereDate('tanggal', Carbon::parse($dates[0])->format('Y-m-d'));
            }
        }

        // Filter by perusahaan
        if($this->filtered_perusahaan){
            $query->where('perusahaan_id', $this->filtered_perusahaan);
        }

        return $query;
    }

    public function map($rekap): array
    {
        $this->rowNumber++;

        // Ambil deviasi dari tabel presensi
        $deviasi_masuk = '-';
        $deviasi_keluar = '-';

        if ($rekap->employee_id) {
            $presensi_masuk = \App\Models\Presensi::where('employee_id', $rekap->employee_id)
                ->where('tanggal_presensi', $rekap->tanggal)
                ->where('jenis_presensi', 'masuk')
                ->value('akurasi_gps');
            
            $presensi_pulang = \App\Models\Presensi::where('employee_id', $rekap->employee_id)
                ->where('tanggal_presensi', $rekap->tanggal)
                ->where('jenis_presensi', 'pulang')
                ->value('akurasi_gps');

            $deviasi_masuk = $presensi_masuk ? number_format($presensi_masuk, 2) : '-';
            $deviasi_keluar = $presensi_pulang ? number_format($presensi_pulang, 2) : '-';
        }

        return [
            $this->rowNumber,
            $rekap->tanggal ? Carbon::parse($rekap->tanggal)->format('d-M-Y') : '-',
            $rekap->employee->nama ?? '-',
            $rekap->employee->no_ktp ?? '-',
            $rekap->perusahaan->nama_perusahaan ?? '-', // Ambil dari relasi perusahaan
            $rekap->waktu_masuk ? Carbon::parse($rekap->waktu_masuk)->format('H:i') : '-',
            $rekap->waktu_pulang ? Carbon::parse($rekap->waktu_pulang)->format('H:i') : '-',
            $deviasi_masuk,
            $deviasi_keluar,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Tgl Absen',
            'Nama',
            'NIK',
            'Penempatan',
            'Jam Masuk',
            'Jam Keluar',
            'Deviasi Masuk',
            'Deviasi Keluar',
        ];
    }
}