<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas\Kelas;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();

        // Ambil semua kelas yang diikuti oleh mahasiswa
        $kelasmahasiswa = $user->kelasMahasiswa()->with('dosen')->get();

        // Ambil ID dari kelas yang diikuti
        $kelasIds = $kelasmahasiswa->pluck('id')->toArray();

        // Ambil semua tugas dari kelas yang diikuti
        $tugasAktif = Tugas::whereIn('kelas_id', $kelasIds)->get();

        // Cek tugas yang sudah dikumpulkan oleh mahasiswa
        $tugasSudahDikumpulkan = PengumpulanTugas::where('mahasiswa_id', $user->id)
            ->pluck('tugas_id')
            ->toArray();

        // ================ STATISTIK PENGUMPULAN TUGAS ================
        $totalTugas = Tugas::whereIn('kelas_id', $kelasIds)->count();
        $jumlahTerkumpul = PengumpulanTugas::where('mahasiswa_id', $user->id)
            ->whereIn('tugas_id', $tugasAktif->pluck('id'))
            ->count();

        $statistik = [
            'total' => [
                'total_tugas' => $totalTugas,
                'terkumpul' => $jumlahTerkumpul,
            ]
        ];

        // ============================================================

        return view('mahasiswa.dashboard', compact(
            'kelasmahasiswa',
            'tugasAktif',
            'tugasSudahDikumpulkan',
            'statistik'
        ));
    }
}
