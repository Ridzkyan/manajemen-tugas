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

        // ===== Cek jumlah total hasil =====
        $count_materi = $materi->count();
        $count_tugas = $tugas->count();
        $count_kelas = $kelas->count();
        $count_komunikasi = $komunikasi->count();
        $count_rekap = $rekap->count();
        $count_pengaturan = $pengaturan ? 1 : 0;

        $totalHasil = $count_materi + $count_tugas + $count_kelas + $count_komunikasi + $count_rekap + $count_pengaturan;

        // ==== Redirect jika hanya ada 1 hasil saja ====
        if ($totalHasil === 1) {
            if ($count_materi === 1) {
                return redirect()->route('materi.show', $materi->first()->id);
            }
            if ($count_tugas === 1) {
                return redirect()->route('tugas_ujian.detail', [
                    'kelas' => $tugas->first()->kelas_id, 
                    'tugas' => $tugas->first()->id
                ]);

            }
            if ($count_kelas === 1) {
                return redirect()->route('kelas.show', $kelas->first()->id);
            }
            if ($count_komunikasi === 1) {
                // Ganti dengan route forum jika ada
                return redirect()->route('forum.kelas', $komunikasi->first()->id);
            }
            if ($count_rekap === 1) {
                // Ganti dengan route rekap jika ada
                return redirect()->route('rekap.show', $rekap->first()->id);
            }
            if ($count_pengaturan === 1) {
                return redirect()->route('dosen.pengaturan');
            }
        }

        // ==== Jika lebih dari 1/ada kombinasi hasil ====
        return view('dosen.search', compact('q', 'materi', 'tugas', 'kelas', 'komunikasi', 'rekap', 'pengaturan'));
    }
}
