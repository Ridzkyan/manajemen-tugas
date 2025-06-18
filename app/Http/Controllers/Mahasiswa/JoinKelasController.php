<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Kelas;
use Illuminate\Support\Facades\Auth;
use App\Models\Tugas\Tugas; 
use App\Models\Kelas\KelasMahasiswa;

/**
 * Class JoinKelasController
 * @package App\Http\Controllers\Mahasiswa
 */
class JoinKelasController extends Controller
{
    /**
     * Tampilkan form input kode unik (halaman join).
     */
    public function index()
    {
        return view('mahasiswa.kelas.join.index');
    }

    /**
     * Tampilkan detail isi kelas yang diikuti mahasiswa.
     */
    public function show($id)
{
    $kelas = Kelas::findOrFail($id);
    $tugas = Tugas::where('kelas_id', $id)->get();

    return view('mahasiswa.kelas.show', compact('kelas', 'tugas'));
}



   public function store(Request $request)
{
    $request->validate([
        'kode_unik' => 'required|exists:kelas,kode_unik',
    ]);

    $kelas = Kelas::where('kode_unik', $request->kode_unik)->first();

    // Cek apakah mahasiswa sudah tergabung
    $sudahGabung = KelasMahasiswa::where('kelas_id', $kelas->id)
        ->where('mahasiswa_id', Auth::id())
        ->exists();

    if ($sudahGabung) {
        return redirect()->back()->with('kelas_error', 'Kamu sudah bergabung ke kelas ini!');
    }

    // Tambahkan ke pivot table kelas_mahasiswa
    KelasMahasiswa::create([
        'kelas_id' => $kelas->id,
        'mahasiswa_id' => Auth::id(),
    ]);

    // Update route name agar konsisten dengan template
    return redirect()->route('mahasiswa.join.index')->with('kelas_success', 'Berhasil bergabung ke kelas!');
}}