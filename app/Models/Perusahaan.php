<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perusahaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perusahaan';

    protected $fillable = [
        'kode_perusahaan',
        'nama_perusahaan',
        'alamat',
        'tanggal_awal_mou',
        'tanggal_akhir_mou',
        'berkas_mou',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_awal_mou' => 'date:Y-m-d',
        'tanggal_akhir_mou' => 'date:Y-m-d',
    ];

    // Relationships
    public function divisi()
    {
        return $this->hasMany(Divisi::class);
    }

    public function historyMou()
    {
        return $this->hasMany(HistoryMou::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function rekapPresensiHarian()
    {
        return $this->hasMany(RekapPresensiHarian::class);
    }

    public function settingJamKerja()
    {
        return $this->hasMany(SettingJamKerja::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'tidak_aktif');
    }

    // Accessors
    public function getBerkasMouUrlAttribute()
    {
        return $this->berkas_mou ? asset('storage/' . $this->berkas_mou) : null;
    }

    // Methods
    public function isMouActive()
    {
        if (!$this->tanggal_awal_mou || !$this->tanggal_akhir_mou) {
            return false;
        }
        
        $today = now()->toDateString();
        return $today >= $this->tanggal_awal_mou && $today <= $this->tanggal_akhir_mou;
    }

    public function getMouDaysRemaining()
    {
        if (!$this->tanggal_akhir_mou) {
            return null;
        }
        
        return now()->diffInDays($this->tanggal_akhir_mou, false);
    }

    // Scope untuk perusahaan aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk perusahaan tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('status', 'tidak_aktif');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'perusahaan_id');
    }

    public function karyawanAktif()
    {
        return $this->hasManyThrough(
            Employee::class,
            EmployeeEmployment::class,
            'perusahaan', // Foreign key on employment_histories table (nama perusahaan)
            'id', // Foreign key on employees table
            'nama_perusahaan', // Local key on perusahaan table
            'employee_id' // Local key on employment_histories table
        )
        ->where('employees.status_active', '1')
        ->where('employee_employment_histories.status', 'aktif')
        ->whereRaw('employee_employment_histories.id IN (
            SELECT MAX(id) 
            FROM employee_employment_histories 
            GROUP BY employee_id
        )');
    }

    // Accessor untuk menghitung karyawan aktif
    public function getTotalKaryawanAktifAttribute()
    {
        return EmployeeEmployment::where('perusahaan', $this->nama_perusahaan)
            ->where('status', 'aktif')
            ->whereHas('employee', function ($q) {
                $q->where('status_active', '1');
            })
            ->whereRaw('id IN (
                SELECT MAX(id) 
                FROM employee_employment_histories 
                GROUP BY employee_id
            )')
            ->count();
    }


    // Check apakah perusahaan punya karyawan aktif
    public function hasActiveEmployees()
    {
        return EmployeeEmployment::active()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->where('perusahaan', $this->nama_perusahaan)
            ->exists();
    }
}