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
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(6);  // 7 hari terakhir termasuk hari ini
        $monthAgo = $today->copy()->subDays(29); // 30 hari terakhir

        $statistik = [
            'harian' => [
                'tanggal' => $today->format('Y-m-d'),
                'terkumpul' => PengumpulanTugas::where('mahasiswa_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'total_tugas' => Tugas::whereIn('kelas_id', $kelasIds)
                    ->whereDate('deadline', $today)
                    ->count(),
            ],
            'mingguan' => [
                'tanggal' => [$weekAgo->format('Y-m-d'), $today->format('Y-m-d')],
                'terkumpul' => PengumpulanTugas::where('mahasiswa_id', $user->id)
                    ->whereBetween('created_at', [$weekAgo, $today])
                    ->count(),
                'total_tugas' => Tugas::whereIn('kelas_id', $kelasIds)
                    ->whereBetween('deadline', [$weekAgo, $today])
                    ->count(),
            ],
            'bulanan' => [
                'tanggal' => [$monthAgo->format('Y-m-d'), $today->format('Y-m-d')],
                'terkumpul' => PengumpulanTugas::where('mahasiswa_id', $user->id)
                    ->whereBetween('created_at', [$monthAgo, $today])
                    ->count(),
                'total_tugas' => Tugas::whereIn('kelas_id', $kelasIds)
                    ->whereBetween('deadline', [$monthAgo, $today])
                    ->count(),
            ],
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
