<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deduction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pot_makan' => 'decimal:2',
        'pot_bpjs_tk' => 'decimal:2',
        'pot_bpjs_kes' => 'decimal:2',
        'pot_bpjs' => 'decimal:2',
        'pot_koperasi' => 'decimal:2',
        'pot_bonus_gantung' => 'decimal:2',
        'pot_jam_kerja' => 'decimal:2',
        'pot_materai' => 'decimal:2',
        'pot_kerusakan' => 'decimal:2',
        'pot_admin' => 'decimal:2',
        'pot_apd' => 'decimal:2',
        'pot_alfa' => 'decimal:2',
        'pot_jamsos' => 'decimal:2',
        'pot_sptp' => 'decimal:2',
        'pot_payroll' => 'decimal:2',
        'pot_seragam' => 'decimal:2',
        'pot_tdk_masuk_jml' => 'decimal:2',
        'pot_tdk_finger' => 'decimal:2',
        'pot_pph21' => 'decimal:2',
        'pot_hari_mingu' => 'decimal:2',
        'pot_lain' => 'decimal:2',
        'klaim' => 'decimal:2',
        'denda' => 'decimal:2',
        'denda_telat_briefing' => 'decimal:2',
        'kas' => 'decimal:2',
        'kasbon' => 'decimal:2',
        'mangkir_jml' => 'decimal:2',
        'terlambat_jml' => 'decimal:2',
        'koreksi_gaji_minus' => 'decimal:2',
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
    public function getTotalPotonganAttribute()
    {
        return ($this->pot_makan ?? 0) + 
               ($this->pot_bpjs_tk ?? 0) + 
               ($this->pot_bpjs_kes ?? 0) + 
               ($this->pot_bpjs ?? 0) + 
               ($this->pot_koperasi ?? 0) + 
               ($this->pot_bonus_gantung ?? 0) + 
               ($this->pot_jam_kerja ?? 0) + 
               ($this->pot_materai ?? 0) + 
               ($this->pot_kerusakan ?? 0) + 
               ($this->pot_admin ?? 0) + 
               ($this->pot_apd ?? 0) + 
               ($this->pot_alfa ?? 0) + 
               ($this->pot_jamsos ?? 0) + 
               ($this->pot_sptp ?? 0) + 
               ($this->pot_payroll ?? 0) + 
               ($this->pot_seragam ?? 0) + 
               ($this->pot_tdk_masuk_jml ?? 0) + 
               ($this->pot_tdk_finger ?? 0) + 
               ($this->pot_pph21 ?? 0) + 
               ($this->pot_hari_mingu ?? 0) + 
               ($this->pot_lain ?? 0) + 
               ($this->klaim ?? 0) + 
               ($this->denda ?? 0) + 
               ($this->denda_telat_briefing ?? 0) + 
               ($this->kas ?? 0) + 
               ($this->kasbon ?? 0) + 
               ($this->mangkir_jml ?? 0) + 
               ($this->terlambat_jml ?? 0) + 
               ($this->koreksi_gaji_minus ?? 0);
    }
}