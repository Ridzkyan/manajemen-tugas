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
        $user = auth()->user(); // User yang sedang login

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && file_exists(public_path($user->foto))) {
                unlink(public_path($user->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Folder berdasarkan role
            $folder = 'images/' . $user->role;

            // Buat folder jika belum ada
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0755, true);
            }

            $file->move(public_path($folder), $filename);

            // Simpan path relatif di DB
            $user->foto = $folder . '/' . $filename;
        }

        $user->save();

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
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();

        if (!Hash::check($request->current_password, $mahasiswa->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $mahasiswa->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
