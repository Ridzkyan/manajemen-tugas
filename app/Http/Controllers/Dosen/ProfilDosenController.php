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
}
