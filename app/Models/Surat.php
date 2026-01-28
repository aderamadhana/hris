<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'file_path',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];
}
