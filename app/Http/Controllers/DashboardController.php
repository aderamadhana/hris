<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use App\Models\EmployeeEmployment;
use Carbon\Carbon;
use App\Models\Perusahaan;
use App\Models\Divisi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     */
    public function index()
    {
        // Proteksi ekstra (walau idealnya pakai middleware)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user()->load([
            'employee',
            'role',
        ]);
        
        return Inertia::render('Dashboard', [
            'user' => $user,
        ]);
    }

    public function getStats(Request $request)
    {
        try {
            // Get filter parameters
            $kontrakHabisFilter = (int) $request->input('kontrak_habis', 30);
            $karyawanBaruFilter = (int) $request->input('karyawan_baru', 7);
            $pelamarFilter      = (int) $request->input('pelamar', 7);
            $resignFilter       = (int) $request->input('resign', 7);
            $perusahaanFilter       = (int) $request->input('perusahaan', 0);

            $data = [
                // Stats Cards
                'stats' => [
                    'karyawanAktif' => $this->getKaryawanAktif(),
                    'karyawanTidakAktif' => $this->getKaryawanTidakAktif(),
                    'kontrakHampirHabis' => $this->getKontrakHampirHabis($kontrakHabisFilter),
                    'clientAktif' => $this->getClientAktif(),
                    'clientTidakAktif' => $this->getClientTidakAktif(),
                    'karyawanBaru' => $this->getKaryawanBaru($karyawanBaruFilter),
                    'pelamarMasuk' => $this->getPelamarMasuk($pelamarFilter),
                    'resign' => $this->getResign($resignFilter),
                ],
                
                // Filter values (echo back untuk sync dengan frontend)
                'filters' => [
                    'kontrakHabis' => $kontrakHabisFilter,
                    'karyawanBaru' => $karyawanBaruFilter,
                    'pelamar' => $pelamarFilter,
                    'resign' => $resignFilter,
                ],
                
                // Chart Data
                'employeeTrend' => $this->getEmployeeTrend(),
                'employeeStatus' => $this->getEmployeeStatus(),
                'departmentData' => $this->getDepartmentData($perusahaanFilter),
                'recruitmentFunnel' => $this->getKontrakHampirHabisPerTanggal($kontrakHabisFilter), // TAMBAHKAN INI
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => $th->getMessage(),
                'line' => $th->getLine()
            ], 500);
        }
    }


    private function getKaryawanAktif()
    {
        return Employee::active()->count();
    }

    /**
     * Get Karyawan Tidak Aktif
     */
    private function getKaryawanTidakAktif()
    {
        return Employee::inactive()->count();
    }

    /**
     * Get Kontrak Hampir Habis
     * @param int $days - jumlah hari untuk filter kontrak hampir habis
     */
    private function getKontrakHampirHabis($days = 30)
    {
        $today = Carbon::now();
        $limitDate = Carbon::now()->addDays($days);
        
        return EmployeeEmployment::active()
            ->pkwt()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->whereNotNull('tgl_akhir_kerja')
            ->whereRaw("STR_TO_DATE(tgl_akhir_kerja, '%Y-%m-%d') BETWEEN ? AND ?", 
                [$today->format('Y-m-d'), $limitDate->format('Y-m-d')])
            ->distinct('employee_id')
            ->count();
    }

    /**
     * Get Client Aktif
     * Client dianggap aktif jika masih memiliki karyawan aktif
     */
    private function getClientAktif()
    {
        return Perusahaan::active()
            ->whereHas('divisi')
            ->get()
            ->filter(function($perusahaan) {
                return $perusahaan->hasActiveEmployees();
            })
            ->count();
    }

    /**
     * Get Client Tidak Aktif
     */
    private function getClientTidakAktif()
    {
        return Perusahaan::inactive()->count();
    }

    /**
     * Get Karyawan Baru
     * @param int $days - jumlah hari untuk filter karyawan baru
     */
    private function getKaryawanBaru($days = 7)
    {
        $dateLimit = Carbon::now()->subDays($days);
        
        return EmployeeEmployment::active()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->whereNotNull('tgl_awal_kerja')
            ->whereRaw("STR_TO_DATE(tgl_awal_kerja, '%Y-%m-%d') >= ?", 
                [$dateLimit->format('Y-m-d')])
            ->distinct('employee_id')
            ->count();
    }

    /**
     * Get Pelamar Masuk
     * Catatan: Ini memerlukan tabel applicants/recruitment
     */
    private function getPelamarMasuk($days = 7)
    {
        // Jika sudah ada Model Applicant:
        /*
        $dateLimit = Carbon::now()->subDays($days);
        return Applicant::where('created_at', '>=', $dateLimit)->count();
        */
        
        return 0; // Sementara return 0
    }

    /**
     * Get Resign
     * @param int $days - jumlah hari untuk filter resign
     */
    private function getResign($days = 7)
    {
        $dateLimit = Carbon::now()->subDays($days);
        
        return EmployeeEmployment::resign()
            ->whereNotNull('tgl_akhir_kerja')
            ->whereRaw("STR_TO_DATE(tgl_akhir_kerja, '%Y-%m-%d') >= ?", 
                [$dateLimit->format('Y-m-d')])
            ->distinct('employee_id')
            ->count();
    }

    /**
     * BONUS: Get Employee Trend Data (untuk chart)
     * Data 6 bulan terakhir
     */
    public function getEmployeeTrend()
    {
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            
            // Karyawan masuk bulan ini
            $masuk = EmployeeEmployment::whereRaw(
                "STR_TO_DATE(tgl_awal_kerja, '%Y-%m-%d') BETWEEN ? AND ?", 
                [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
            )->distinct('employee_id')->count();
            
            // Karyawan keluar bulan ini
            $keluar = EmployeeEmployment::resign()
                ->whereRaw(
                    "STR_TO_DATE(tgl_akhir_kerja, '%Y-%m-%d') BETWEEN ? AND ?", 
                    [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
                )
                ->distinct('employee_id')->count();
            
            // Total karyawan aktif di akhir bulan
            $total = Employee::active()
                ->where('created_at', '<=', $endDate)
                ->count();
            
            $trends[] = [
                'bulan' => $month->format('M'),
                'masuk' => $masuk,
                'keluar' => $keluar,
                'total' => $total,
            ];
        }
        
        return $trends;
    }

    /**
     * BONUS: Get Employee Status Distribution
     */
    public function getEmployeeStatus()
    {
        $statuses = [];
        
        // 1. Get PKWT (Kontrak)
        $pkwt = EmployeeEmployment::active()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->where('jenis_kontrak', 'PKWT')
            ->distinct('employee_id')
            ->count();
        
        if ($pkwt > 0) {
            $statuses[] = [
                'name' => 'PKWT',
                'value' => $pkwt,
                'color' => '#3b82f6',
            ];
        }
        
        // 2. Get PKWTT (Tetap)
        $pkwtt = EmployeeEmployment::active()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->where('jenis_kontrak', 'PKWTT')
            ->distinct('employee_id')
            ->count();
        
        if ($pkwtt > 0) {
            $statuses[] = [
                'name' => 'PKWTT',
                'value' => $pkwtt,
                'color' => '#10b981',
            ];
        }
        
        // 3. Get Pelamar (status_active = 0)
        $pelamar = Employee::inactive()->count();
        
        if ($pelamar > 0) {
            $statuses[] = [
                'name' => 'Pelamar',
                'value' => $pelamar,
                'color' => '#f59e0b',
            ];
        }
        
        // 4. Get Tidak Terdefinisi (jenis_kontrak = null atau selain PKWT/PKWTT)
        $tidakTerdefinisi = EmployeeEmployment::active()
            ->whereHas('employee', function($q) {
                $q->active();
            })
            ->where(function($q) {
                $q->whereNull('jenis_kontrak')
                ->orWhereNotIn('jenis_kontrak', ['PKWT', 'PKWTT']);
            })
            ->distinct('employee_id')
            ->count();
        
        if ($tidakTerdefinisi > 0) {
            $statuses[] = [
                'name' => 'Tidak Terdefinisi',
                'value' => $tidakTerdefinisi,
                'color' => '#6b7280',
            ];
        }
        
        return $statuses;
    }

    /**
     * BONUS: Get Department Data
     */
    public function getDepartmentData($perusahaanId)
    {
        return EmployeeEmployment::query()
            ->whereHas('employee', fn ($q) => $q->active())
            ->join('divisi', 'divisi.nama_divisi', '=', 'employee_employment_histories.penempatan')
            ->where('divisi.perusahaan_id', $perusahaanId)
            ->where('divisi.status', 'aktif')
            ->selectRaw('
                divisi.nama_divisi as dept,
                COUNT(DISTINCT employee_employment_histories.employee_id) as total
            ')
            ->groupBy('divisi.nama_divisi')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    /**
     * BONUS: Get Recruitment Funnel
     * Data 7 hari terakhir pelamar masuk per hari
     */
    public function getRecruitmentFunnel()
    {
        // Jika sudah ada Model Applicant:
        /*
        $funnel = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $count = Applicant::whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            
            $funnel[] = [
                'tanggal' => $date->format('d M'),
                'jumlah' => $count,
            ];
        }
        
        return $funnel;
        */
        
        return []; // Sementara return array kosong
    }

    private function getKontrakHampirHabisPerTanggal($days = 30)
    {
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays($days);

        $kontrakData = EmployeeEmployment::query()
            ->selectRaw('DATE(tgl_akhir_kerja) as tanggal, COUNT(*) as jumlah')
            // ->where('status', 'aktif')
            ->whereNotNull('tgl_akhir_kerja')
            ->whereBetween(
                DB::raw('DATE(tgl_akhir_kerja)'),
                [$today->toDateString(), $endDate->toDateString()]
            )
            ->whereHas('employee', function ($q) {
                $q->where('status_active', '1');
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Generate semua tanggal (termasuk yang kosong)
        $result = [];
        $currentDate = $today->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            $existingData = $kontrakData->firstWhere('tanggal', $dateStr);

            $result[] = [
                'tanggal'        => $currentDate->format('d M'),
                'tanggal_full'   => $dateStr,
                'jumlah'         => $existingData ? (int) $existingData->jumlah : 0,
            ];

            $currentDate->addDay();
        }

        return $result;
    }
}
