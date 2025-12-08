<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Cari employee berdasarkan NRP (NIK)
        $employee = Employee::where('nrp', $request->nik)->first();

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
}