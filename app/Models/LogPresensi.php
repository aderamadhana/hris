<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPresensi extends Model
{
    use HasFactory;

    protected $table = 'log_presensi';

    protected $fillable = [
        'presensi_id',
        'karyawan_id',
        'aksi',
        'data_sebelum',
        'data_sesudah',
        'alasan',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
    ];

    // Relationships
    public function presensi()
    {
        return $this->belongsTo(Presensi::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeCreate($query)
    {
        return $query->where('aksi', 'create');
    }

    public function scopeUpdate($query)
    {
        return $query->where('aksi', 'update');
    }

    public function scopeDelete($query)
    {
        return $query->where('aksi', 'delete');
    }

    public function scopeKoreksi($query)
    {
        return $query->where('aksi', 'koreksi');
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    // Methods
    public function getPerubahanAttribute()
    {
        if (!$this->data_sebelum || !$this->data_sesudah) {
            return null;
        }

        $perubahan = [];
        
        foreach ($this->data_sesudah as $key => $valueBaru) {
            $valueLama = $this->data_sebelum[$key] ?? null;
            
            if ($valueLama != $valueBaru) {
                $perubahan[$key] = [
                    'dari' => $valueLama,
                    'menjadi' => $valueBaru
                ];
            }
        }

        return $perubahan;
    }
}