<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use App\Models\KelasMahasiswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   public function index()
{
    $user = auth()->user();

    // Kelas yang diikuti mahasiswa
    $kelasmahasiswa = $user->kelasMahasiswa()->with('dosen')->get()->unique('id');

    // Ambil ID kelas
    $kelasIds = $kelasmahasiswa->pluck('id');

    // Ambil semua tugas aktif dari kelas yang diikuti
    $tugasAktif = Tugas::whereIn('kelas_id', $kelasIds)
        ->whereDate('deadline', '>=', now())
        ->with('kelas')
        ->orderBy('deadline')
        ->take(3)
        ->get();

    // Ambil ID tugas yang sudah dikumpulkan oleh mahasiswa
    $tugasSudahDikumpulkan = PengumpulanTugas::where('mahasiswa_id', auth()->id())
        ->whereIn('kelas_id', $kelasIds)
        ->pluck('tugas_id')
        ->toArray();

    return view('mahasiswa.dashboard', compact('kelasmahasiswa', 'tugasAktif', 'tugasSudahDikumpulkan'));
}
}