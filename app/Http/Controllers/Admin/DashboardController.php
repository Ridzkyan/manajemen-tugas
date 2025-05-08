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
    
        // Ambil list kelas (bisa batasi 5 saja kalau mau)
        $daftarKelas = Kelas::with('dosen')->latest()->take(5)->get();
    
        $materiTerbaru = Materi::with('kelas')->latest()->take(5)->get();
        
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
            'daftarKelas',
            'materiTerbaru',
            'kelasTeraktif'
        ));
    }

    public function monitoring()
    {
        $kelasTeraktif = Kelas::withCount('materi')
            ->orderByDesc('materi_count')
            ->take(5)
            ->get();
    
        $threshold = now()->subMinutes(5); // dianggap online jika login dalam 5 menit terakhir
    
        $allUsers = User::select('name', 'email', 'role', 'last_login_at')
            ->orderBy('role')
            ->get()
            ->groupBy('role');
    
        return view('admin.monitoring.index', compact(
            'kelasTeraktif',
            'allUsers',
            'threshold'
        ));
    }
}
