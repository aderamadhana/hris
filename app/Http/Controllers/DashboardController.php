<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
}
