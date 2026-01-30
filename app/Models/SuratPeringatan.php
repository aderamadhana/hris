<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SuratPeringatan extends Model
{
    protected $fillable = [
        'nomor_sp',
        'tanggal_sp',
        'employee_id',
        'no_ktp',
        'tingkat',
        'pelanggaran',
        'tanggal_kejadian',
        'file_path',
        'periode_bulan',
        // jangan perlu di-fill dari request, tapi boleh tetap ada di fillable kalau kamu pakai mass-assign internal
        'tanggal_berakhir',
    ];

    protected $casts = [
        'tanggal_sp' => 'date',
        'tanggal_kejadian' => 'date',
        'tanggal_berakhir' => 'date',
        'periode_bulan' => 'integer',
    ];

    // biar otomatis muncul di JSON: file_url
    protected $appends = ['file_url'];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    /**
     * Accessor: file_url (untuk frontend)
     * Pastikan disk sesuai cara kamu simpan file (public / s3 / dll).
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;

        // kalau file_path sudah berupa URL penuh, return langsung
        if (preg_match('/^https?:\/\//i', $this->file_path)) {
            return $this->file_path;
        }

        // asumsi file disimpan di disk 'public'
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Hitung tanggal_berakhir dari tanggal_sp + periode_bulan
     * Dijalankan otomatis saat create/update.
     */
    protected static function booted()
    {
        static::saving(function (SuratPeringatan $sp) {
            // normalisasi periode_bulan null/0
            $months = (int) ($sp->periode_bulan ?? 0);

            if ($sp->tanggal_sp && $months > 0) {
                // Carbon dari cast date sudah Carbon instance
                $tanggalSp = $sp->tanggal_sp instanceof Carbon
                    ? $sp->tanggal_sp
                    : Carbon::parse($sp->tanggal_sp);

                // addMonthsNoOverflow: aman untuk tanggal 29/30/31
                $sp->tanggal_berakhir = $tanggalSp->copy()->addMonthsNoOverflow($months);
            } else {
                $sp->tanggal_berakhir = null;
            }

            // OPTIONAL: kalau kamu mau snapshot no_ktp dari employee
            // (hindari mismatch kalau employee berubah, tapi ini jadi data duplikat)
            if ($sp->employee && empty($sp->no_ktp)) {
                $sp->no_ktp = $sp->employee->no_ktp ?? null;
            }
        });
    }
}
