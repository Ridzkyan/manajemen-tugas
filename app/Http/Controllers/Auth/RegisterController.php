<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User\Mahasiswa;
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
        // Menyimpan data mahasiswa dengan email_verified_at NULL
        return Mahasiswa::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['name'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => null, // Email belum terverifikasi
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

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login.mahasiswa')->with('success', 'Akun berhasil dibuat! Silakan cek email kamu untuk verifikasi.');
    }

    /**
     * Tampilkan form registrasi.
     */
    public function showRegistrationForm()
    {
        return view('auth.mahasiswa-register'); // atau 'auth.mahasiswa-register'
    }
}
