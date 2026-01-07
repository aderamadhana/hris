<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingJamKerja extends Model
{
    use HasFactory;

    protected $table = 'setting_jam_kerja';

    protected $fillable = [
        'perusahaan_id',
        'divisi_id',
        'jam_masuk',
        'jam_pulang',
        'toleransi_terlambat',
        'is_default',
        'hari_kerja',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
        'toleransi_terlambat' => 'integer',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByPerusahaan($query, $perusahaanId)
    {
        return $query->where('perusahaan_id', $perusahaanId);
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    // Methods
    public function isTerlambat($waktuMasuk)
    {
        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $waktuPresensi = \Carbon\Carbon::parse($waktuMasuk);
        
        $selisihMenit = $jamMasuk->diffInMinutes($waktuPresensi, false);
        
        return $selisihMenit > $this->toleransi_terlambat;
    }

    public function getJamKerjaFormatted()
    {
        return sprintf(
            '%s - %s',
            \Carbon\Carbon::parse($this->jam_masuk)->format('H:i'),
            \Carbon\Carbon::parse($this->jam_pulang)->format('H:i')
        );
    }
}