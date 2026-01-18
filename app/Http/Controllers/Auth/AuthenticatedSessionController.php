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
        $seo_description = "Masuk ke akun Anda di Si-GOchild untuk mengakses berbagai layanan dan informasi penting tentang website kami.";
        $seo_meta_title = "Login - Si-Gochild";
        $seo_title = "Login - Si-Gochild";
        $seo_key = 'login si-gochild, login Al Jannah, akses akun si-gochild, preschool login, si-gochild services login, al jannah login, login al jannah, login si-gochild al jannah, login si-gochild kedungwuni, pendaftaran al jannah, pendaftaran si-gochild, pendaftaran si-gochild kedungwuni, pendaftaran si-gochild al jannah';

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
