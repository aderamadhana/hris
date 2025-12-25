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

class EmployeesExport extends StringValueBinder
    implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize
{
    private int $rowNumber = 0;
    public function __construct(
        protected ?string $search,
        protected int $statusActive,
        protected ?string $filteredJabatan,
        protected ?string $filteredPerusahaan,
    ) {}
    
    public function query()
    {
        return Employee::query()
            ->with('employments')
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nrp', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%")
                      ->orWhere('no_ktp', 'like', "%{$search}%")
                      ->orWhereHas('employments', function ($qe) use ($search) {
                          $qe->where('jabatan', 'like', "%{$search}%");
                      });
                });
            })
            ->when($this->filteredJabatan || $this->filteredPerusahaan, function ($query) {
                $query->whereHas('employments', function ($qe) {
                    $qe->when($this->filteredJabatan, function ($q) {
                        $q->where('penempatan', $this->filteredJabatan);
                    })
                    ->when($this->filteredPerusahaan, function ($q) {
                        $q->where('perusahaan', $this->filteredPerusahaan);
                    });
                });
            })
            ->where('status_active', $this->statusActive)
            ->whereNotIn('id', [1,2])
            ->orderBy('nama');
    }

    public function map($employee): array
    {
        $this->rowNumber++;

        $education = $employee->educations
            ->sortByDesc('tahun_lulus')
            ->first();

        $ayah = $employee->families->firstWhere('hubungan', 'Ayah');
        $ibu  = $employee->families->firstWhere('hubungan', 'Ibu');

        $pasangan = $employee->families
            ->whereIn('hubungan', ['Suami', 'Istri'])
            ->first();

        $anak = $employee->families
            ->where('hubungan', 'Anak')
            ->values();

        $latestJob = $employee->employmentss
            ->sortByDesc('tgl_awal_kerja')
            ->first();

        $today = Carbon::today();

        // Usia
        $usia = null;

        if ($employee->tanggal_lahir) {
            $diff = Carbon::parse($employee->tanggal_lahir)->diff(Carbon::today());

            $usia = sprintf(
                '%d Tahun %d Bulan %d Hari',
                $diff->y,
                $diff->m,
                $diff->d
            );
        }

        $masaKerja = null;

        if (optional($latestJob)->tgl_awal_kerja) {
            $diff = Carbon::parse($latestJob->tgl_awal_kerja)->diff(Carbon::today());

            $masaKerja = sprintf(
                '%d Tahun %d Bulan %d Hari',
                $diff->y,
                $diff->m,
                $diff->d
            );
        }

        // Helper format tanggal
        $fd = fn ($date) => $date ? Carbon::parse($date)->format('d/m/Y') : null;

        return [
            // === IDENTITAS ===
            (string) $this->rowNumber,
            (string) $employee->nrp,
            (string) $employee->user_id,
            (string) $employee->nama,
            (string) $employee->jenis_kelamin,
            (string) optional($employee->personal)->no_ktp,
            (string) $employee->tempat_lahir,
            $fd($employee->tanggal_lahir),

            // === ALAMAT ===
            null,
            null,
            null,
            optional($employee->address)->desa,
            optional($employee->address)->kecamatan,
            optional($employee->address)->kota,
            optional($employee->address)->kode_pos,
            optional($employee->address)->alamat_lengkap,
            null,

            // === KONTAK ===
            (string) optional($employee->personal)->no_wa,
            optional($employee->personal)->email,

            // === BPJS & FASKES ===
            optional($employee->personal)->bpjs_tk,
            optional($employee->personal)->x,
            optional($employee->personal)->bpjs_kes,
            optional($employee->personal)->x_ks,
            optional($employee->personal)->nama_faskes,

            // === SKCK & LISENSI ===
            optional($employee->personal)->no_skck,
            $fd(optional($employee->personal)->masa_berlaku_skck),

            optional($employee->personal)->jenis_lisensi,
            optional($employee->personal)->no_lisensi,
            $fd(optional($employee->personal)->masa_berlaku_lisensi),

            // === KEUANGAN ===
            optional($employee->personal)->no_rekening,
            optional($employee->personal)->no_cif,
            optional($employee->personal)->bank,
            optional($employee->personal)->npwp,
            optional($employee->personal)->ptkp,

            // === STATUS PERSONAL ===
            optional($employee->personal)->agama,
            optional($employee->personal)->status_perkawinan,
            optional($employee->personal)->kewarganegaraan,

            // === PENDIDIKAN ===
            optional($education)->tahun_lulus,
            optional($education)->jurusan,
            optional($education)->sekolah_asal,

            // === KESEHATAN ===
            $fd(optional($employee->health)->tanggal_mcu),
            optional($employee->health)->tinggi_badan,
            optional($employee->health)->berat_badan,
            optional($employee->health)->gol_darah,
            optional($employee->health)->darah,
            optional($employee->health)->urine,
            optional($employee->health)->f_hati,
            optional($employee->health)->gula_darah,
            optional($employee->health)->ginjal,
            optional($employee->health)->thorax,
            optional($employee->health)->tensi,
            optional($employee->health)->nadi,
            optional($employee->health)->buta_warna,
            optional($employee->health)->od,
            optional($employee->health)->os,
            optional($employee->health)->riwayat_penyakit,

            $fd(optional($employee->health)->tanggal_drug_test),
            optional($employee->health)->hasil_drug_test,

            // === KELUARGA ===
            optional($employee->personal)->no_kk,
            optional($ayah)->nama,
            optional($ibu)->nama,
            optional($pasangan)->nama,
            optional($pasangan)->no_ktp,
            optional($pasangan)->jenis_kelamin,
            optional($pasangan)->tempat_lahir,
            $fd(optional($pasangan)->tanggal_lahir),
            optional($pasangan)->pendidikan,
            optional($pasangan)->pekerjaan,
            $fd(optional($pasangan)->tgl_perkawinan),

            optional($anak->get(0))->nama,
            optional($anak->get(1))->nama,
            optional($anak->get(2))->nama,
            $anak->count(),

            // === PEKERJAAN ===
            optional($latestJob)->perusahaan,
            optional($latestJob)->penempatan,
            optional($latestJob)->no_kontrak,
            optional($latestJob)->cost_center,
            $fd(optional($latestJob)->tgl_daftar),
            $fd(optional($latestJob)->tgl_awal_kerja),
            $fd(optional($latestJob)->tgl_akhir_kerja),
            optional($latestJob)->jenis_kontrak,
            optional($latestJob)->status,
            optional($latestJob)->keterangan_status,
            optional($latestJob)->job_roll,

            // === RINGKASAN ===
            $usia,
            $masaKerja,
            optional($latestJob)->pola_kerja,
            optional($latestJob)->jenis_kerja,
            optional($latestJob)->hari_kerja,

            // === ATRIBUT TAMBAHAN ===
            optional($employee->personal)->shoe_size,
            optional($employee->personal)->uniform_size,
            optional($employee->personal)->gp,
            optional($employee->personal)->via,
            optional($employee->personal)->reg_digantikan,
            optional($employee->personal)->nama_digantikan,

            // === STATUS ===
            $employee->status_active,
            null,
        ];
    }


    public function headings(): array
    {
        return [
            'No',
            'NRP',
            'User ID',
            'Nama',
            'JK',
            'No. KTP',
            'Tempat Lahir',
            'Tanggal Lahir',

            'Jl / Gg / Dsn / Dkh / Perum',
            'RT',
            'RW',
            'Desa / Kelurahan',
            'Kecamatan',
            'Kota / Kabupaten',
            'Kode Pos',
            'Alamat Lengkap',
            'Alamat Tinggal / Asal',

            'WA Aktif / Tlp',
            'E-mail',

            'BPJS TK',
            'x',
            'BPJS KES',
            'x KS',
            'Nama Faskes',

            'Catatan Kepolisian (SKCK)',
            'Masa Berlaku',

            'Jenis Lisensi',
            'No Lisensi (SIO / SIM / Lainnya)',
            'Berlaku',

            'No Rekening',
            'No CIF',
            'Bank',
            'NPWP',
            'PTKP',

            'Agama',
            'Status Perkawinan',
            'Kewarganegaraan',

            'Tahun Lulus',
            'Jurusan',
            'Sekolah Asal',

            'Tgl MCU',
            'TB',
            'BB',
            'GD',
            'Darah',
            'Urine',
            'F Hati',
            'Gula Darah (Creatinin)',
            'F Ginjal (Puasa)',
            'Thorax',
            'Tensi',
            'Nadi',
            'Buta Warna',
            'OD',
            'OS',
            'Riwayat Sakit',

            'Tgl Drug Test',
            'Drug Test',

            'No. Kartu Keluarga',
            'Nama Ayah Kandung',
            'Nama Ibu Kandung',

            'Suami / Istri',
            'No. KTP Suami / Istri',
            'JK Istri',
            'Tempat Lahir Suami / Istri',
            'Tanggal Lahir Suami / Istri',
            'Pendidikan',
            'Pekerjaan',
            'Tgl Perkawinan',

            'Anak ke-1',
            'Anak ke-2',
            'Anak ke-3',
            'Jumlah Anak',

            'Perusahaan',
            'Penempatan / Bagian',
            'No Kontrak',
            'Cost Center',
            'Tgl Daftar',
            'Tgl Awal Kerja',
            'Tgl Akhir Kerja',
            'Jenis Kontrak',
            'Status',
            'Keterangan Status',
            'Job Roll',

            'Usia',
            'Masa Kerja',
            'Pola Kerja',
            'Jenis Kerja',
            'Hari Kerja',

            'Shoes Size',
            'Uniform Size',
            'GP',
            'VIA',
            'Reg Digantikan',
            'Nama Digantikan',

            '1/0',
            'Keterangan',
        ];
    }

}
