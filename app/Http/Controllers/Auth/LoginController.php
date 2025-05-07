<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Tampilkan form login untuk dosen dan mahasiswa.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login untuk dosen dan mahasiswa.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'role'     => 'required|in:dosen,mahasiswa', // admin DIHAPUS
        ]);

        $credentials = $request->only('email', 'password');
        $guard = $request->role;

        if (Auth::guard($guard)->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            switch ($guard) {
                case 'dosen':
                    return redirect()->intended(route('dosen.dashboard'));
                case 'mahasiswa':
                    return redirect()->intended(route('mahasiswa.dashboard'));
                default:
                    return redirect('/');
            }
            
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
    

    /**
     * Logout dari guard aktif (dosen/mahasiswa).
     */
    public function logout(Request $request)
    {
        foreach (['dosen', 'mahasiswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
