<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\Materi;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    /**
     * Tampilkan daftar materi untuk suatu kelas.
     */
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        // Ambil semua materi tanpa filter 'status'
        $materi = Materi::where('kelas_id', $kelasId)->get();

        return view('mahasiswa.kelas.materi.index', [
            'kelas' => $kelas,
            'materis' => $materi
        ]);
    }

    /**
     * Tampilkan daftar kelas yang diikuti mahasiswa.
     */
    public function daftarKelasMateri()
    {
        $user = Auth::guard('mahasiswa')->user();

        $kelasmahasiswa = $user->kelasMahasiswa()
            ->with('dosen')
            ->get()
            ->unique('id');

        return view('mahasiswa.kelas.index', compact('kelasmahasiswa'));
    }
}
