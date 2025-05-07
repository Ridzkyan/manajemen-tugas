<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    // Menampilkan daftar semua kelas dosen
    public function index()
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->get();
        return view('dosen.dashboard', compact('kelas'));
    }

    // Menampilkan form tambah kelas baru
    public function create()
    {
        return view('dosen.kelas.create');
    }

    // Menyimpan data kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'nama_matakuliah' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url' // validasi untuk link whatsapp
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'nama_matakuliah' => $request->nama_matakuliah,
            'kode_unik' => 'KLS-' . rand(10000, 99999),
            'dosen_id' => Auth::id(),
            'whatsapp_link' => $request->whatsapp_link, // menyimpan link whatsapp
        ]);

        return redirect()->route('dosen.kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

    // Menampilkan detail kelas dan daftar mahasiswa
    public function show($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        $mahasiswa = $kelas->mahasiswa; // relasi belongsToMany

        return view('dosen.kelas.mahasiswa', compact('kelas', 'mahasiswa'));
    }

    // Menampilkan form edit kelas
    public function edit($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        return view('dosen.kelas.edit', compact('kelas'));
    }

    // Update data kelas
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'nama_matakuliah' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url',
        ]);

        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'nama_matakuliah' => $request->nama_matakuliah,
            'whatsapp_link' => $request->whatsapp_link,
        ]);

        return redirect()->route('dosen.dashboard')->with('success', 'Kelas berhasil diupdate!');
    }

    // Menghapus kelas
    public function destroy($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        $kelas->delete();

        return redirect()->route('dosen.dashboard')->with('success', 'Kelas berhasil dihapus!');
    }

    // Menampilkan halaman manage kelas
    public function manage($id)
    {
        $kelas = \App\Models\Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        return view('dosen.kelas.manage', compact('kelas'));
    }
    
}
