<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalEarning extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'anjem_jam' => 'decimal:2',
        'anjem_jml' => 'decimal:2',
        'borongan_kg' => 'decimal:2',
        'borongan_jml' => 'decimal:2',
        'retase' => 'decimal:2',
        'retase_bongkar' => 'decimal:2',
        'piket_l_biasa' => 'decimal:2',
        'piket_l_besar' => 'decimal:2',
        'piket_l_lain' => 'decimal:2',
        'piket_bbm' => 'decimal:2',
        'piket_reguler' => 'decimal:2',
        'piket_hari_raya' => 'decimal:2',
        'upah_hr_nasional' => 'decimal:2',
        'upah_hr_raya' => 'decimal:2',
        'lmbr_hr_nasional' => 'decimal:2',
        'bonus' => 'decimal:2',
        'premi' => 'decimal:2',
        'insentif' => 'decimal:2',
        'insentif_malam' => 'decimal:2',
        'perdin' => 'decimal:2',
        'pengiriman' => 'decimal:2',
        'uang_extra' => 'decimal:2',
        'accident' => 'decimal:2',
        'pelatihan_gaji' => 'decimal:2',
        'rapelan' => 'decimal:2',
        'kurang_bulan_lalu' => 'decimal:2',
        'koreksi_gaji_plus' => 'decimal:2',
        'koreksi_pph21' => 'decimal:2',
        'pengembalian_pph21' => 'decimal:2',
        'pembulatan' => 'decimal:2',
        'lain_lain' => 'decimal:2',
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
    public function getTotalAdditionalAttribute()
    {
        return ($this->anjem_jml ?? 0) + 
               ($this->borongan_jml ?? 0) + 
               ($this->retase ?? 0) + 
               ($this->retase_bongkar ?? 0) + 
               ($this->piket_l_biasa ?? 0) + 
               ($this->piket_l_besar ?? 0) + 
               ($this->piket_l_lain ?? 0) + 
               ($this->piket_bbm ?? 0) + 
               ($this->piket_reguler ?? 0) + 
               ($this->piket_hari_raya ?? 0) + 
               ($this->upah_hr_nasional ?? 0) + 
               ($this->upah_hr_raya ?? 0) + 
               ($this->lmbr_hr_nasional ?? 0) + 
               ($this->bonus ?? 0) + 
               ($this->premi ?? 0) + 
               ($this->insentif ?? 0) + 
               ($this->insentif_malam ?? 0) + 
               ($this->perdin ?? 0) + 
               ($this->pengiriman ?? 0) + 
               ($this->uang_extra ?? 0) + 
               ($this->accident ?? 0) + 
               ($this->pelatihan_gaji ?? 0) + 
               ($this->rapelan ?? 0) + 
               ($this->kurang_bulan_lalu ?? 0) + 
               ($this->koreksi_gaji_plus ?? 0) + 
               ($this->koreksi_pph21 ?? 0) + 
               ($this->pengembalian_pph21 ?? 0) + 
               ($this->pembulatan ?? 0) + 
               ($this->lain_lain ?? 0);
    }
}