<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeSummary extends Model
{
    use HasFactory;

    protected $table = 'overtime_summary';
    protected $guarded = [];

    protected $casts = [
        'overtime_jam' => 'decimal:2',
        'lembur_jam' => 'decimal:2',
        'lembur_jam_biasa' => 'decimal:2',
        'lembur_jam_khusus' => 'decimal:2',
        'lembur_minggu_2' => 'decimal:2',
        'lembur_minggu_3' => 'decimal:2',
        'lembur_minggu_4' => 'decimal:2',
        'lembur_minggu_5' => 'decimal:2',
        'lembur_minggu_6' => 'decimal:2',
        'lembur_minggu_7' => 'decimal:2',
        'lembur_libur' => 'decimal:2',
        'lembur_2' => 'decimal:2',
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
    public function getTotalLemburAttribute()
    {
        return $this->lembur_minggu_2 + $this->lembur_minggu_3 + 
               $this->lembur_minggu_4 + $this->lembur_minggu_5 + 
               $this->lembur_minggu_6 + $this->lembur_minggu_7;
    }
}