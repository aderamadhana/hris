<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktifitas extends Model
{
    use HasFactory;

    protected $table = 'aktifitas';

    protected $fillable = [
        'kode',
        'nama_aktifitas',
    ];

    /**
     * Relasi: 1 aktifitas punya banyak log aktifitas
     */
    public function logAktifitas()
    {
        return $this->hasMany(LogAktifitas::class, 'aktifitas_id');
    }
}
