<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gaji_hk' => 'decimal:2',
        'gaji_hl' => 'decimal:2',
        'gaji_hr' => 'decimal:2',
        'gaji_jml' => 'decimal:2',
        'gaji_train_jml' => 'decimal:2',
        'gaji_rev' => 'decimal:2',
        'gaji_lbh_tgl23_bulan_lalu' => 'decimal:2',
        'lembur_jml' => 'decimal:2',
        'lembur_jml_hk' => 'decimal:2',
        'lembur_jml_hl' => 'decimal:2',
        'lembur_jml_hr' => 'decimal:2',
        'lembur_biasa_jml' => 'decimal:2',
        'lembur_khusus_jml' => 'decimal:2',
        'lembur_kurang_bulan_lalu' => 'decimal:2',
        'overtime' => 'decimal:2',
        'fee_lembur' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    // Accessor
    public function getTotalGajiAttribute()
    {
        return ($this->gaji_hk ?? 0) + ($this->gaji_hl ?? 0) + ($this->gaji_hr ?? 0);
    }

    public function getTotalLemburAttribute()
    {
        return ($this->lembur_jml_hk ?? 0) + ($this->lembur_jml_hl ?? 0) + ($this->lembur_jml_hr ?? 0);
    }
}