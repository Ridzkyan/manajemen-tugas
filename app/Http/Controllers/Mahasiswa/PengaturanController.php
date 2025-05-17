<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    /**
     * Halaman utama pengaturan
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();
        return view('mahasiswa.pengaturan.index', compact('user'));
    }

    /**
     * Form edit profil
     */
    public function editProfile()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        return view('mahasiswa.pengaturan.profile-edit', compact('mahasiswa'));
    }

    /**
     * Update data profil mahasiswa
     */
    public function updateProfile(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto'), $namaFile);
            $mahasiswa->foto = 'uploads/foto/' . $namaFile;
        }

        $mahasiswa->nama = $request->nama;
        $mahasiswa->email = $request->email;
        $mahasiswa->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Form ganti password
     */
    public function editPassword()
    {
        return view('mahasiswa.pengaturan.password-edit');
    }

    /**
     * Simpan password baru
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();

        if (!Hash::check($request->current_password, $mahasiswa->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }

        $mahasiswa->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
