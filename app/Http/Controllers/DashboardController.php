<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use App\Models\EmployeeEmployment;
use Carbon\Carbon;

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

            $stats = [
                // HR - Karyawan Tidak Aktif
                'karyawanAktif' => $this->getKaryawanAktif(),
                'karyawanTidakAktif' => $this->getKaryawanTidakAktif(),
                
                // Kontrak - Hampir Habis
                'kontrakHampirHabis' => $this->getKontrakHampirHabis($kontrakHabisFilter),
                
                // Client - Aktif (berdasarkan employee yang masih aktif dengan perusahaan tertentu)
                'clientAktif' => $this->getClientAktif(),
                
                // Client - Tidak Aktif
                'clientTidakAktif' => $this->getClientTidakAktif(),
                
                // HR - Karyawan Baru
                'karyawanBaru' => $this->getKaryawanBaru($karyawanBaruFilter),
                
                // Rekrutmen - Pelamar Masuk (ini perlu tabel recruitment/applicants terpisah)
                // Untuk sementara saya set 0, karena tidak ada tabel pelamar di skema
                'pelamarMasuk' => $this->getPelamarMasuk($pelamarFilter),
                
                // HR - Resign
                'resign' => $this->getResign($resignFilter),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Throwable $th) {
            
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => $th->getMessage(),
                'line' => $th->getLine()
            ]);
        }
        
    }

    /**
     * Hitung karyawan tidak aktif (status_active = '0' atau 'N')
     */
    private function getKaryawanAktif()
    {
        return Employee::whereIn('status_active', ['1', 'N', null])
            ->count();
    }

    private function getKaryawanTidakAktif()
    {
        return Employee::whereIn('status_active', ['0', 'N', null])
            ->orWhere('status_kary', 'like', '%tidak aktif%')
            ->orWhere('status_kary', 'like', '%PHK%')
            ->orWhere('status_kary', 'like', '%resign%')
            ->count();
    }

    /**
     * Hitung kontrak yang hampir habis dalam X hari ke depan
     */
    private function getKontrakHampirHabis($days)
    {
        $dateLimit = Carbon::now()->addDays($days);
        
        return EmployeeEmployment::where('status', 'Aktif')
            ->whereNotNull('tgl_akhir_kerja')
            ->where('tgl_akhir_kerja', '<=', $dateLimit)
            ->where('tgl_akhir_kerja', '>=', Carbon::now())
            ->where(function($query) {
                $query->where('jenis_kontrak', 'PKWT')
                      ->orWhereNull('jenis_kontrak');
            })
            ->count();
    }

    /**
     * Hitung jumlah client/perusahaan yang masih memiliki karyawan aktif
     */
    private function getClientAktif()
    {
        return 0;
        // return EmployeeEmployment::where('status', 'aktif')
        //     ->whereHas('employee', function($query) {
        //         $query->where('status_active', '1')
        //               ->orWhere('status_active', 'Y');
        //     })
        //     ->distinct('perusahaan')
        //     ->count('perusahaan');
    }

    /**
     * Hitung jumlah client/perusahaan yang tidak memiliki karyawan aktif lagi
     */
    private function getClientTidakAktif()
    {
        return 0;
        // // Ambil semua perusahaan
        // $allCompanies = EmployeeEmployment::distinct('perusahaan')
        //     ->pluck('perusahaan');
        
        // // Ambil perusahaan yang masih aktif
        // $activeCompanies = EmployeeEmployment::where('status', 'aktif')
        //     ->whereHas('employee', function($query) {
        //         $query->where('status_active', '1')
        //               ->orWhere('status_active', 'Y');
        //     })
        //     ->distinct('perusahaan')
        //     ->pluck('perusahaan');
        
        // // Hitung selisihnya
        // return $allCompanies->diff($activeCompanies)->count();
    }

    /**
     * Hitung karyawan baru dalam X hari terakhir
     */
    private function getKaryawanBaru($days)
    {
        $dateFrom = Carbon::now()->subDays($days);
        
        return EmployeeEmployment::where('status', 'aktif')
            ->where('tgl_awal_kerja', '>=', $dateFrom)
            ->whereHas('employee', function($query) {
                $query->where('status_active', '1')
                      ->orWhere('status_active', 'Y');
            })
            ->count();
    }

    /**
     * Hitung pelamar masuk dalam X hari terakhir
     * Note: Ini membutuhkan tabel applicants/recruitment terpisah
     * Untuk sementara return 0
     */
    private function getPelamarMasuk($days)
    {
        // TODO: Implement ketika ada tabel applicants
        // $dateFrom = Carbon::now()->subDays($days);
        // return Applicant::where('created_at', '>=', $dateFrom)->count();
        
        return 0;
    }

    /**
     * Hitung karyawan resign dalam X hari terakhir
     */
    private function getResign($days)
    {
        $dateFrom = Carbon::now()->subDays($days);
        
        return EmployeeEmployment::whereIn('status', ['resign', 'keluar', 'berhenti'])
            ->where('tgl_akhir_kerja', '>=', $dateFrom)
            ->where('tgl_akhir_kerja', '<=', Carbon::now())
            ->count();
    }
}
