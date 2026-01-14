<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;

class PerusahaanExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithCustomValueBinder,
    ShouldAutoSize
{
    private string $search;
    private string $status;
    private int $no = 0;

    public function __construct($search = '', $status = '')
    {
        $this->search = trim((string) $search);
        $this->status = trim((string) $status);
    }

    public function bindValue(Cell $cell, $value)
    {
        // Paksa beberapa kolom jadi string (hindari Excel auto-format)
        // A:No, B:Kode, C:Nama, G:Berkas
        $col = $cell->getColumn();
        if (in_array($col, ['B', 'C', 'G'], true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function query()
    {
        // 1) Group perusahaan dari history
        $grouped = DB::table('employee_employment_histories as eeh')
            ->select('eeh.perusahaan as nama_perusahaan')
            ->selectRaw('COUNT(*) as total_history')
            ->groupBy('eeh.perusahaan');

        if ($this->search !== '') {
            $grouped->where('eeh.perusahaan', 'like', "%{$this->search}%");
        }

        // 2) Subquery: latest employment per employee
        $latest = DB::table('employee_employment_histories')
            ->selectRaw('employee_id, MAX(id) as max_id')
            ->groupBy('employee_id');

        // 3) Hitung karyawan aktif berdasarkan employment terbaru
        $activeCounts = DB::table('employees as e')
            ->joinSub($latest, 'l', function ($join) {
                $join->on('e.id', '=', 'l.employee_id');
            })
            ->join('employee_employment_histories as eeh2', 'eeh2.id', '=', 'l.max_id')
            ->where('e.status_active', '1')
            ->select('eeh2.perusahaan')
            ->selectRaw('COUNT(*) as total_karyawan_aktif')
            ->groupBy('eeh2.perusahaan');

        // 4) Gabungkan semuanya + join perusahaan (exact)
        $q = DB::query()
            ->fromSub($grouped, 'g')
            ->leftJoinSub($activeCounts, 'ac', function ($join) {
                $join->on('ac.perusahaan', '=', 'g.nama_perusahaan');
            })
            ->leftJoin('perusahaan as p', 'p.nama_perusahaan', '=', 'g.nama_perusahaan')
            ->select([
                'g.nama_perusahaan',
                'g.total_history',
                DB::raw('COALESCE(ac.total_karyawan_aktif, 0) as total_karyawan_aktif'),

                'p.id',
                'p.kode_perusahaan',
                'p.alamat',
                'p.tanggal_awal_mou',
                'p.tanggal_akhir_mou',
                'p.berkas_mou',
                'p.keterangan',
                'p.status',
                'p.created_at',
                'p.updated_at',
            ])
            ->orderByDesc('g.total_history');

        // Filter status (default controller kamu ngasih fallback 'aktif')
        if ($this->status !== '') {
            $q->whereRaw("COALESCE(p.status, 'aktif') = ?", [$this->status]);
        }

        return $q;
    }

    public function map($row): array
    {
        $this->no++;

        // Fallback: kalau join exact tidak dapat data perusahaan, coba LIKE (seperti controller)
        $p = null;
        if ($row->id === null) {
            $p = DB::table('perusahaan')
                ->where('nama_perusahaan', $row->nama_perusahaan)
                ->orWhere('nama_perusahaan', 'like', '%' . $row->nama_perusahaan . '%')
                ->first();
        }

        $kode   = $row->kode_perusahaan ?? ($p->kode_perusahaan ?? '-');
        $alamat = $row->alamat ?? ($p->alamat ?? '-');

        $awal  = $row->tanggal_awal_mou ?? ($p->tanggal_awal_mou ?? null);
        $akhir = $row->tanggal_akhir_mou ?? ($p->tanggal_akhir_mou ?? null);

        $berkas = $row->berkas_mou ?? ($p->berkas_mou ?? null);
        $ket    = $row->keterangan ?? ($p->keterangan ?? null);
        $status = $row->status ?? ($p->status ?? 'aktif');

        $created = $row->created_at ?? ($p->created_at ?? null);
        $updated = $row->updated_at ?? ($p->updated_at ?? null);

        return [
            $this->no,
            (string) $kode,
            (string) $row->nama_perusahaan,
            (string) $alamat,
            $awal  ? Carbon::parse($awal)->format('Y-m-d') : '',
            $akhir ? Carbon::parse($akhir)->format('Y-m-d') : '',
            (int) $row->total_karyawan_aktif,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Perusahaan',
            'Nama Perusahaan',
            'Alamat',
            'Tanggal Awal MOU',
            'Tanggal Akhir MOU',
            'Total Karyawan Aktif',
        ];
    }
}
