<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryConfiguration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'effective_date' => 'date',
        'gaji_pokok' => 'decimal:2',
        'gaji_per_hari' => 'decimal:2',
        'gaji_train_hk' => 'decimal:2',
        'gaji_train_upah_per_jam' => 'decimal:2',
        'lembur_per_hari' => 'decimal:2',
        'lembur_per_jam' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopeEffectiveAt($query, $date)
    {
        return $query->where('effective_date', '<=', $date)
                     ->orderBy('effective_date', 'desc');
    }
}