<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Loker extends Model
{
    use SoftDeletes;

    protected $table = 'lokers';

    protected $fillable = [
        'judul',
        'slug',
        'tipe_pekerjaan',
        'perusahaan_id',
        'perusahaan_nama',
        'penempatan_id',
        'penempatan_nama',
        'jam_kerja',
        'ringkasan',
        'deskripsi',
        'persyaratan',
        'gaji_min',
        'gaji_max',
        'mata_uang',
        'link_lamar',
        'whatsapp_kontak',
        'aktif',
        'tanggal_publish',
        'tanggal_berakhir',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_publish' => 'datetime',
        'tanggal_berakhir' => 'datetime',
        'gaji_min' => 'integer',
        'gaji_max' => 'integer',
    ];

    /* =========================
       Scope Query (biar rapi)
    ========================= */

    // hanya yang aktif & sudah publish
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function scopeSudahPublish(Builder $query): Builder
    {
        return $query->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now());
    }

    // tidak kadaluarsa (tanggal_berakhir null atau masih future)
    public function scopeBelumBerakhir(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('tanggal_berakhir')
              ->orWhere('tanggal_berakhir', '>=', now());
        });
    }
}
