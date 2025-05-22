<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User\Dosen;

class ProfilDosenController extends Controller
{
    public function pengaturan()
    {
        return view('dosen.pengaturan.index');
    }

    public function editProfil()
    {
        $dosen = Auth::guard('dosen')->user();
        return view('dosen.pengaturan.edit_profil', compact('dosen'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $dosen = Auth::guard('dosen')->user();
        $dosen->name = $request->name;
        $dosen->email = $request->email;
        $dosen->save();

        return redirect()->route('dosen.pengaturan')->with('success', 'Profil berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('dosen.pengaturan.edit_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ]);

        $dosen = Auth::guard('dosen')->user();

        if (!Hash::check($request->current_password, $dosen->password)) {
            return back()->with('error', 'Password lama salah.');
        }

        $dosen->password = Hash::make($request->new_password);
        $dosen->save();

        return redirect()->route('dosen.pengaturan')
                         ->with('success', 'Password berhasil diperbarui!');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('dosen')->user();

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
}
