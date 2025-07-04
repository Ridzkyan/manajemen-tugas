<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\Kelas\Kelas;

class MonitoringController extends Controller
{
    public function index()
    {
        $kelasTeraktif = Kelas::withCount('materi')
            ->orderByDesc('materi_count')
            ->take(5)
            ->get();
    
            
            $kelasTugasTerbanyak = Kelas::withCount('tugas')
            ->orderByDesc('tugas_count')
            ->take(5)
            ->get();
            
        $allUsers = User::orderBy('role')->get()->groupBy('role');
        
        return view('admin.monitoring.index', compact('allUsers', 'kelasTeraktif', 'kelasTugasTerbanyak'));
    }
}
