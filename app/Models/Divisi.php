<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Divisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'divisi';

    protected $fillable = [
        'perusahaan_id',
        'nama_divisi',
        'kode_divisi',
        'alamat_penempatan',
        'latitude',
        'longitude',
        'radius_presensi',
        'keterangan',
        'status',
        'tanggal_awal_mou',
        'tanggal_akhir_mou',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_presensi' => 'integer',
        'tanggal_awal_mou' => 'date:Y-m-d',
        'tanggal_akhir_mou' => 'date:Y-m-d',
    ];

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function rekapPresensiHarian()
    {
        return $this->hasMany(RekapPresensiHarian::class);
    }

    public function settingJamKerja()
    {
        return $this->hasMany(SettingJamKerja::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'tidak_aktif');
    }

    // Methods
    public function hasGpsCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function calculateDistance($lat, $long)
    {
        if (!$this->hasGpsCoordinates()) {
            return null;
        }

        // Haversine formula untuk menghitung jarak
        $earthRadius = 6371000; // dalam meter

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($long);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // hasil dalam meter
    }

    public function isWithinRadius($lat, $long)
    {
        $distance = $this->calculateDistance($lat, $long);
        
        if (is_null($distance)) {
            return false;
        }

        return $distance <= $this->radius_presensi;
    }
}