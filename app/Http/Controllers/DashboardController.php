<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        // Cek apakah pengguna terautentikasi dengan guard 'student' atau 'web'
        if (Auth::guard('student')->check()) {
            // Jika guard adalah 'student', arahkan ke dashboard-student
            return view('dashboard-student');
        } elseif (Auth::guard('web')->check()) {
            // Jika guard adalah 'web', arahkan ke dashboard
            return view('dashboard');
        }

        // Jika tidak terautentikasi, arahkan ke login page
        return redirect()->route('login')->with('error', 'Please log in first.');
    }
}
