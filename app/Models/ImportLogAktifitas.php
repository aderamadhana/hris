<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLogAktifitas extends Model
{
    protected $table = 'import_log_aktifitas';

    protected $fillable = [
        'file',
        'status',
        'total',
        'success',
        'failed',
        'errors',
    ];

    protected $casts = [
        'total' => 'integer',
        'success' => 'integer',
        'failed' => 'integer',
        // kalau kamu simpan errors sebagai JSON string, ini bisa kamu ubah jadi 'array'
        // tapi itu hanya aman kalau selalu json_encode saat simpan.
        // 'errors' => 'array',
    ];

    // Optional: konstanta status biar konsisten
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';
}
