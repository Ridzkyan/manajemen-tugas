<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\Materi;
use App\Models\User\Mahasiswa;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data dosen login
        $dosen = Auth::guard('dosen')->user();
        $dosenId = $dosen->id;

        // Ambil semua kelas yang diajar oleh dosen beserta mahasiswanya
        $kelas = Kelas::with('mahasiswa')->where('dosen_id', $dosenId)->get();

        // Hitung mahasiswa unik dari semua kelas yang diajar
        $mahasiswaIds = [];
        foreach ($kelas as $kls) {
            foreach ($kls->mahasiswa as $mhs) {
                $mahasiswaIds[] = $mhs->id;
            }
        }
        $userCount = count(array_unique($mahasiswaIds));

        // Hitung jumlah materi yang hanya dari kelas dosen ini
        $materiCount = Materi::whereIn('kelas_id', $kelas->pluck('id'))->count();

        // Hitung jumlah matakuliah unik
        $mataKuliahCount = $kelas->pluck('nama_matakuliah')->unique()->count();

        // Ambil statistik nilai dari tugas per matakuliah
        $statistikNilai = [];
        foreach ($kelas as $kls) {
            $avgNilai = $kls->tugas()->avg('nilai');
            if ($avgNilai !== null) {
                $statistikNilai[$kls->nama_matakuliah] = round($avgNilai, 2);
            }
        }

        return view('dosen.dashboard', compact(
            'dosen', 'userCount', 'materiCount', 'mataKuliahCount', 'kelas', 'statistikNilai'
        ));
    }
}
