<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Tugas;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas= Tugas::where('kelas_id', $kelasId)
            ->where('status', 'disetujui')
            ->get();
    

        return view('mahasiswa.kelas.tugas.index', compact('kelas', 'tugas'));
    }
}
