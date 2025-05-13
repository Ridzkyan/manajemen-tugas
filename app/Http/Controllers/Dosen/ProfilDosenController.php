<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    return back()->with('success', 'Profil berhasil diperbarui.');
    }


    public function editPassword()
    {
        return view('dosen.pengaturan.edit_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $dosen = Auth::guard('dosen')->user();
        $dosen->password = Hash::make($request->password);
        $dosen->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
