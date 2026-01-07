<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'karyawan_id',
        'perusahaan_id',
        'divisi_id',
        'tanggal_presensi',
        'jenis_presensi',
        'waktu_presensi',
        'foto_presensi',
        'latitude',
        'longitude',
        'akurasi_gps',
        'is_valid_location',
        'jarak_dari_lokasi',
        'status',
        'keterangan',
        'device_info',
        'ip_address',
    ];

    protected $casts = [
        'tanggal_presensi' => 'date',
        'waktu_presensi' => 'datetime:H:i:s',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'akurasi_gps' => 'decimal:2',
        'jarak_dari_lokasi' => 'decimal:2',
        'is_valid_location' => 'boolean',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function logPresensi()
    {
        return $this->hasMany(LogPresensi::class);
    }

    // Scopes
    public function scopeMasuk($query)
    {
        return $query->where('jenis_presensi', 'masuk');
    }

    public function scopePulang($query)
    {
        return $query->where('jenis_presensi', 'pulang');
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }

    public function scopeDiluarRadius($query)
    {
        return $query->where('status', 'diluar_radius');
    }

    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_presensi', $tanggal);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_presensi', now()->month)
                     ->whereYear('tanggal_presensi', now()->year);
    }

    // Accessors
    public function getFotoPresensiUrlAttribute()
    {
        return $this->foto_presensi ? asset('storage/' . $this->foto_presensi) : null;
    }

    // Methods
    public function validateLocation()
    {
        if (!$this->divisi->hasGpsCoordinates()) {
            return [
                'valid' => false,
                'message' => 'Lokasi divisi belum diset'
            ];
        }

        $jarak = $this->divisi->calculateDistance($this->latitude, $this->longitude);
        $this->jarak_dari_lokasi = $jarak;
        $this->is_valid_location = $jarak <= $this->divisi->radius_presensi;

        return [
            'valid' => $this->is_valid_location,
            'jarak' => $jarak,
            'radius' => $this->divisi->radius_presensi
        ];
    }
}