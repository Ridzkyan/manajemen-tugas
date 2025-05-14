<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:mahasiswa')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.mahasiswa-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('mahasiswa')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('mahasiswa')->user();
            $user->is_online = true;
            $user->last_login_at = now();
            $user->save();

            return redirect()->intended(route('mahasiswa.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();

        if ($user) {
            $user->is_online = false;
            $user->save();
        }

        Auth::guard('mahasiswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
