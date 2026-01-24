<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Pengumuman extends Model
{
    use SoftDeletes;

    protected $table = 'pengumumans';

    protected $fillable = [
        'kategori',
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'diutamakan',
        'aktif',
        'tanggal_publish',
        'tanggal_berakhir',
        'urutan',
    ];

    protected $casts = [
        'diutamakan' => 'boolean',
        'aktif' => 'boolean',
        'tanggal_publish' => 'datetime',
        'tanggal_berakhir' => 'datetime',
        'urutan' => 'integer',
    ];

    /* =========================
       Scope Query
    ========================= */

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function scopeSudahPublish(Builder $query): Builder
    {
        return $query->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now());
    }

    public function scopeBelumBerakhir(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('tanggal_berakhir')
              ->orWhere('tanggal_berakhir', '>=', now());
        });
    }

    // pinned dulu, lalu terbaru
    public function scopeUrutLanding(Builder $query): Builder
    {
        return $query->orderByDesc('diutamakan')
            ->orderBy('urutan')
            ->orderByDesc('tanggal_publish');
    }
}
