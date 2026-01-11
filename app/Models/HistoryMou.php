<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMou extends Model
{
    use HasFactory;

    protected $table = 'history_mou';

    protected $fillable = [
        'perusahaan_id',
        'tanggal_awal',
        'tanggal_akhir',
        'berkas_mou',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
    ];

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Accessors
    public function getBerkasMouUrlAttribute()
    {
        return $this->berkas_mou ? asset('storage/' . $this->berkas_mou) : null;
    }

    // Methods
    public function isActive()
    {
        $today = now()->toDateString();
        return $today >= $this->tanggal_awal && $today <= $this->tanggal_akhir && $this->status === 'aktif';
    }
}