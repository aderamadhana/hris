<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allowance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tunj' => 'decimal:2',
        'tunj_sewa_motor' => 'decimal:2',
        'tunj_bbm' => 'decimal:2',
        'tunj_pulsa' => 'decimal:2',
        'tunj_penampilan' => 'decimal:2',
        'tunj_shift' => 'decimal:2',
        'tunj_makan' => 'decimal:2',
        'tunj_transport' => 'decimal:2',
        'tunj_kost' => 'decimal:2',
        'tunj_maintenance' => 'decimal:2',
        'tunj_posisi' => 'decimal:2',
        'tunj_fisik' => 'decimal:2',
        'tunj_loyalitas' => 'decimal:2',
        'tunj_operator' => 'decimal:2',
        'tunj_jabatan' => 'decimal:2',
        'tunj_bag' => 'decimal:2',
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
    public function getTotalTunjanganAttribute()
    {
        return ($this->tunj ?? 0) + 
               ($this->tunj_sewa_motor ?? 0) + 
               ($this->tunj_bbm ?? 0) + 
               ($this->tunj_pulsa ?? 0) + 
               ($this->tunj_penampilan ?? 0) + 
               ($this->tunj_shift ?? 0) + 
               ($this->tunj_makan ?? 0) + 
               ($this->tunj_transport ?? 0) + 
               ($this->tunj_kost ?? 0) + 
               ($this->tunj_maintenance ?? 0) + 
               ($this->tunj_posisi ?? 0) + 
               ($this->tunj_fisik ?? 0) + 
               ($this->tunj_loyalitas ?? 0) + 
               ($this->tunj_operator ?? 0) + 
               ($this->tunj_jabatan ?? 0) + 
               ($this->tunj_bag ?? 0);
    }
}