<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        $seo_description = "Masuk ke akun Anda di Al Jannah Preschool and Day Care untuk mengakses berbagai layanan dan informasi penting tentang kegiatan daycare kami.";
        $seo_meta_title = "Login - Al Jannah Preschool and Day Care";
        $seo_title = "Login - Al Jannah Preschool and Day Care";
        $seo_key = 'login daycare, login Al Jannah, akses akun daycare, preschool login, daycare services login, al jannah login, login al jannah, login daycare al jannah, login daycare kedungwuni, pendaftaran al jannah, pendaftaran daycare, pendaftaran daycare kedungwuni, pendaftaran daycare al jannah';

        return view('auth.login', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Simpan informasi guard ke session
        session(['guard' => Auth::getDefaultDriver()]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }


    public function destroy(Request $request): RedirectResponse
    {
        // Logout dari guard 'web' (untuk user biasa)
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Logout dari guard 'student' (untuk akun student)
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        }

        // Menghapus sesi dan me-refresh token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Mengarahkan kembali ke halaman depan setelah logout
        return redirect('/');
    }
}
