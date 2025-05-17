<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginDosenController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:dosen')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.dosen-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'kode_unik' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $user = \App\Models\User\Dosen::where('email', $credentials['email'])->first();

        // Cek kode unik
        if (!$user || $user->kode_unik !== $request->kode_unik) {
            return back()->withInput()->with('error', 'Kode Unik tidak sesuai atau kosong.');
        }

        // Cek login
        if (Auth::guard('dosen')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('dosen')->user();
            $user->is_online = true;
            $user->last_login_at = now();
            $user->save();

            return redirect()->intended(route('dosen.dashboard'));
        }

        // Jika login gagal
        return back()->withInput()->with('error', 'Email atau password salah.');
    }

    protected function redirectTo()
    {
        return route('dosen.dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('dosen')->user();

        if ($user) {
            $user->is_online = false;
            $user->save();
        }

        Auth::guard('dosen')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dosen.login')->with('message', 'Anda telah logout.');
    }
}
