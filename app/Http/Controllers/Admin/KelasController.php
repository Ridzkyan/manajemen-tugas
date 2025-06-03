<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $daftarKelas = Kelas::with(['dosen', 'mahasiswa', 'materis', 'tugas'])->latest()->get();

        return view('admin.kelas_matakuliah.index', compact('daftarKelas'));
    }
}
