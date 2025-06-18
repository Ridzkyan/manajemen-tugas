<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\Materi;
use App\Models\User\Mahasiswa;
use App\Models\Tugas\Tugas;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->guard('dosen')->check()) {
        abort(403, 'Unauthorized');
    }
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

        // Hitung jumlah materi dari semua kelas milik dosen
        $materiCount = Materi::whereIn('kelas_id', $kelas->pluck('id'))->count();

        // Hitung jumlah mata kuliah unik
        $mataKuliahCount = $kelas->pluck('nama_matakuliah')->unique()->count();

        // Statistik Rata-rata Nilai per tugas (semua tugas dari kelas dosen ini)
        $tugasList = Tugas::with('pengumpulanTugas')
            ->whereIn('kelas_id', $kelas->pluck('id'))
            ->get();

        $statistikNilai = $tugasList->map(function ($tugas) {
            $nilai = $tugas->pengumpulanTugas->pluck('nilai')->filter(); // hanya nilai yang sudah dinilai
            return [
                'judul' => $tugas->judul,
                'rata' => $nilai->count() > 0 ? round($nilai->avg(), 2) : 0,
            ];
        });

        return view('dosen.dashboard', compact(
            'dosen',
            'userCount',
            'materiCount',
            'mataKuliahCount',
            'kelas',
            'statistikNilai'
        ));
    }
}
