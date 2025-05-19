<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Ke mana redirect setelah register.
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Middleware untuk hanya user guest.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validasi input registrasi.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Simpan data user baru.
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'mahasiswa', // default role untuk registrasi mahasiswa
        ]);
    }

    /**
     * Setelah registrasi berhasil.
     */
    protected function registered(Request $request, $user)
    {
        // Logout dulu agar user tidak otomatis login sebelum verifikasi
        Auth::logout();

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('success', 'Akun berhasil dibuat! Silakan cek email kamu untuk verifikasi.');
    }

    public function showRegistrationForm()
    {
        return view('auth.mahasiswa-register'); // atau 'auth.mahasiswa-register'
    }
}
