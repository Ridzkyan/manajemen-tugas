<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;

class UserController extends Controller
{
    // =====================
    // === Manajemen User ===
    // =====================
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'kode_unik' => $request->role === 'dosen' ? $request->kode_unik : null,
        ];

        User::create($data);

        return redirect()->route('admin.dashboard.users')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.dashboard.users')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.dashboard.users')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => bcrypt('password123'),
        ]);

        return redirect()->route('admin.dashboard.users')->with('success', 'Password berhasil direset ke "password123".');
    }

    // ==========================
    // === Edit Profil & Foto ===
    // ==========================
    public function editProfile()
    {
        $admin = auth()->user();
        return view('admin.profil.edit', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto'), $namaFile);
            $admin->foto = 'uploads/foto/' . $namaFile;
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        return redirect()->route('admin.pengaturan')->with('success', 'Profil berhasil diperbarui.');
    }

    // =====================
    // === Ganti Password ===
    // =====================
    public function editPassword()
    {
        return view('admin.profil.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.pengaturan')->with('success', 'Password berhasil diperbarui.');
    }
}
