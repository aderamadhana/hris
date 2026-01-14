<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapPresensiHarian extends Model
{
    use HasFactory;

    protected $table = 'rekap_presensi_harian';

    protected $fillable = [
        'karyawan_id',
        'perusahaan_id',
        'divisi_id',
        'tanggal',
        'waktu_masuk',
        'foto_masuk',
        'lat_masuk',
        'long_masuk',
        'valid_lokasi_masuk',
        'waktu_pulang',
        'foto_pulang',
        'lat_pulang',
        'long_pulang',
        'valid_lokasi_pulang',
        'status_kehadiran',
        'durasi_kerja_menit',
        'keterangan',
        'akurasi_gps_masuk',
        'akurasi_gps_pulang'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
        'waktu_pulang' => 'datetime:H:i:s',
        'lat_masuk' => 'decimal:8',
        'long_masuk' => 'decimal:8',
        'lat_pulang' => 'decimal:8',
        'long_pulang' => 'decimal:8',
        'akurasi_gps_masuk' => 'decimal:2',
        'akurasi_gps_pulang' => 'decimal:2',
        'valid_lokasi_masuk' => 'boolean',
        'valid_lokasi_pulang' => 'boolean',
        'durasi_kerja_menit' => 'integer',
    ];

    // Relationships
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    // Scopes
    public function scopeHadir($query)
    {
        return $query->where('status_kehadiran', 'hadir');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status_kehadiran', 'terlambat');
    }

    public function scopeTidakHadir($query)
    {
        return $query->where('status_kehadiran', 'tidak_hadir');
    }

    public function scopeIzin($query)
    {
        return $query->where('status_kehadiran', 'izin');
    }

    public function scopeSakit($query)
    {
        return $query->where('status_kehadiran', 'sakit');
    }

    public function scopeCuti($query)
    {
        return $query->where('status_kehadiran', 'cuti');
    }

    public function scopeDiluarRadius($query)
    {
        return $query->where('status_kehadiran', 'diluar_radius');
    }

    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    public function scopePeriode($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    // Accessors
    public function getFotoMasukUrlAttribute()
    {
        return $this->foto_masuk ? asset('storage/' . $this->foto_masuk) : null;
    }

    public function getFotoPulangUrlAttribute()
    {
        return $this->foto_pulang ? asset('storage/' . $this->foto_pulang) : null;
    }

    public function getDurasiKerjaJamAttribute()
    {
        if (!$this->durasi_kerja_menit) {
            return null;
        }
        
        $jam = floor($this->durasi_kerja_menit / 60);
        $menit = $this->durasi_kerja_menit % 60;
        
        return sprintf('%d jam %d menit', $jam, $menit);
    }

    // Methods
    public function hitungDurasiKerja()
    {
        if (!$this->waktu_masuk || !$this->waktu_pulang) {
            return 0;
        }

        $masuk = \Carbon\Carbon::parse($this->waktu_masuk);
        $pulang = \Carbon\Carbon::parse($this->waktu_pulang);

        return $masuk->diffInMinutes($pulang);
    }
}