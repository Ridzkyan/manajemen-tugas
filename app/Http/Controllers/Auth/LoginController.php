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
        // Validasi awal
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required|string',
            'role'       => 'required|in:dosen,mahasiswa',
            'kode_unik'  => $request->role === 'dosen' ? 'required' : 'nullable',
        ]);
    
        $credentials = $request->only('email', 'password');
        $guard = $request->role;
    
        // Validasi tambahan untuk dosen: kode unik harus sesuai
        if ($guard === 'dosen') {
            $user = \App\Models\User::where('email', $credentials['email'])->where('role', 'dosen')->first();
    
            if (!$user || $user->kode_unik !== $request->kode_unik) {
                return back()->withErrors([
                    'kode_unik' => 'Kode Unik tidak sesuai atau kosong.',
                ])->withInput(); // agar isian form tidak hilang
            }
        }
    
        // Proses autentikasi
        if (Auth::guard($guard)->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
    
            $user = Auth::guard($guard)->user();
            $user->is_online = true;
            $user->save();
        
            switch ($guard) {
                case 'dosen':
                    return redirect()->intended(route('dosen.dashboard'));
                case 'mahasiswa':
                    return redirect()->intended(route('mahasiswa.dashboard'));
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
                $user = Auth::guard($guard)->user();
                $user->is_online = false;
                $user->save();
        
                Auth::guard($guard)->logout();
            }
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
