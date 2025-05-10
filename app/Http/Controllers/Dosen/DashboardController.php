<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        $userCount = User::count();
        $materiCount = Materi::count();
        $mataKuliahCount = Kelas::where('dosen_id', $dosenId)->distinct('nama_matakuliah')->count();
        $kelas = Kelas::where('dosen_id', $dosenId)->get();

        // Ambil statistik nilai nyata dari tugas tiap mata kuliah
        $statistikNilai = [];
        foreach ($kelas as $kls) {
            $avgNilai = $kls->tugas()->avg('nilai');
            if ($avgNilai !== null) {
                $statistikNilai[$kls->nama_matakuliah] = round($avgNilai, 2);
            }
        }

        return view('dosen.dashboard', compact(
            'userCount', 'materiCount', 'mataKuliahCount', 'kelas', 'statistikNilai'
        ));
    }
}
