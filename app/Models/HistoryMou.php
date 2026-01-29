<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryMou extends Model
{
    use SoftDeletes;

    protected $table = 'history_mous';

    protected $fillable = [
        'perusahaan_id',
        'tanggal_awal_mou',
        'tanggal_akhir_mou',
        'berkas_mou',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_awal_mou' => 'date:Y-m-d',
        'tanggal_akhir_mou' => 'date:Y-m-d',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function getBerkasMouUrlAttribute()
    {
        return $this->berkas_mou ? asset('storage/' . $this->berkas_mou) : null;
    }

    public function isActive(): bool
    {
        $today = now()->startOfDay();
        return $today->between($this->tanggal_awal_mou, $this->tanggal_akhir_mou);
    }
}
