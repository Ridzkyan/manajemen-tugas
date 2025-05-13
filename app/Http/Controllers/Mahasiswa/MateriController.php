<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\KelasMahasiswa;  

class MateriController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
    
        $materi = Materi::where('kelas_id', $kelasId)
            ->where('status', 'disetujui')
            ->get();
    
        return view('mahasiswa.kelas.materi.index', compact('kelas', 'materi'));
    }

    public function daftarKelasMateri()
    {
        $kelasmahasiswa = auth()->user()
            ->kelasMahasiswa
            ->unique('id')
            ->load('dosen');    

        return view('mahasiswa.kelas.index', compact('kelasmahasiswa'));
    }

}
