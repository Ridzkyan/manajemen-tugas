<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Tugas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalKelas = Kelas::count();
        $totalMateri = Materi::count();
        $totalTugas = Tugas::count();

        $jumlahAdmin = User::where('role', 'admin')->count();
        $jumlahDosen = User::where('role', 'dosen')->count();
        $jumlahMahasiswa = User::where('role', 'mahasiswa')->count();

        // Hitung persentase untuk pie chart 3D
        $total = $jumlahAdmin + $jumlahDosen + $jumlahMahasiswa;
        $persenAdmin = $total > 0 ? round(($jumlahAdmin / $total) * 100, 1) : 0;
        $persenDosen = $total > 0 ? round(($jumlahDosen / $total) * 100, 1) : 0;
        $persenMahasiswa = $total > 0 ? round(($jumlahMahasiswa / $total) * 100, 1) : 0;

        // Ambil list kelas
        $daftarKelas = Kelas::with('dosen')->latest()->take(5)->get();

        // Ambil materi terbaru
        $materiTerbaru = Materi::with('kelas')->latest()->take(5)->get();

        // Ambil kelas teraktif berdasarkan jumlah materi
        $kelasTeraktif = Kelas::withCount('materi')
            ->get()
            ->map(function ($item) {
                $item->label = $item->nama_kelas . ' - ' . $item->nama_matakuliah;
                return $item;
            })
            ->sortByDesc('materi_count')
            ->take(5);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalKelas',
            'totalMateri',
            'totalTugas',
            'jumlahAdmin',
            'jumlahDosen',
            'jumlahMahasiswa',
            'persenAdmin',
            'persenDosen',
            'persenMahasiswa',
            'daftarKelas',
            'materiTerbaru',
            'kelasTeraktif'
        ));
    }
}
