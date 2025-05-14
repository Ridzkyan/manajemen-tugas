<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\Komunikasi;
use Illuminate\Http\Request;
use App\Models\KelasMahasiswa as KelasMahasiswaModel;
use App\Models\Kelas as KelasModel;

class KomunikasiController extends Controller
{
    public function index()
    {
        $kelasmahasiswa = auth()->user()->kelasMahasiswa;

        return view('mahasiswa.kelas.komunikasi.index', compact('kelasmahasiswa'));
    }

}