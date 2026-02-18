<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
// use App\Models\EmployeePersonal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function create()
    {
        return Inertia::render('Login');
    }

    /**
     * Proses login
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string', 'min:5'],
        ]);

        // Cari employee berdasarkan NRP (NIK)
        $employee = Employee::where('status_active', 1)->where('no_ktp',$request->nik)->first();

        // Validasi: Employee tidak ditemukan
        if (!$employee) {
            throw ValidationException::withMessages([
                'nik' => 'NIK tidak terdaftar dalam sistem.',
            ]);
        }

        // Validasi: Employee belum memiliki akun user
        if (!$employee->user_id) {
            throw ValidationException::withMessages([
                'nik' => 'NIK belum terdaftar sebagai user. Hubungi admin HR.',
            ]);
        }

        // Attempt login menggunakan user_id dari employee
        $credentials = [
            'id' => $employee->user_id,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
            ]);
        }

        // Login gagal - password salah
        throw ValidationException::withMessages([
            'nik' => 'NIK atau kata sandi yang Anda masukkan salah.',
        ]);
    }

    /**
     * Logout
     */
    public function destroy(Request $request)
    {
        $userName = Auth::user()->name;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah keluar. Sampai jumpa, ' . $userName . '!');
    }

    public function resetPasswordToDefault(){
        $users = EmployeePersonal::whereNotIn('employee_personals.employee_id', [1, 2])
            ->whereNotNull('employee_personals.no_ktp')
            ->join('employees', 'employee_personals.employee_id', '=', 'employees.id')
            ->whereNotNull('employees.user_id')
            ->select('employee_personals.no_ktp', 'employees.user_id')
            ->get();

        $resetCount = 0;

        foreach($users as $user) {
            User::where('id', $user->user_id)
                ->update(['password' => Hash::make($user->no_ktp)]);
            $resetCount++;
        }

        return response()->json([
            'success' => true,
            'message' => $resetCount . ' passwords berhasil direset ke no KTP'
        ]);
    }
}
