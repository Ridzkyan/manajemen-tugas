<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\Materi;
use App\Models\Tugas\Tugas;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = strtolower($request->input('q'));

        $materi = Materi::where('judul', 'like', "%$q%")->get();

        $kelas = Kelas::where('nama_kelas', 'like', "%$q%")
            ->orWhere('nama_matakuliah', 'like', "%$q%")
            ->get();

        $tugas = Tugas::where('judul', 'like', "%$q%")->get();

        // Komunikasi & Rekap diambil dari kelas
        $komunikasi = Kelas::where('nama_kelas', 'like', "%$q%")->get();
        $rekap = Kelas::where('nama_matakuliah', 'like', "%$q%")->get();

        // Manual untuk pengaturan
        $pengaturan = stripos($q, 'pengaturan') !== false;

        return view('dosen.search', compact('q', 'materi', 'tugas', 'kelas', 'komunikasi', 'rekap', 'pengaturan'));
    }
}