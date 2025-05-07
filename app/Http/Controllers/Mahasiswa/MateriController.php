<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Materi;

class MateriController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $materis = Materi::where('kelas_id', $kelasId)->get();

        return view('mahasiswa.kelas.materi.index', compact('kelas', 'materis'));
    }
}
