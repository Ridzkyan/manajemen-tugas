<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Relasi ke kelas dan dosen dimuat langsung
    $kelasmahasiswa = $user->Kelas('dosen')->get();

    return view('mahasiswa.dashboard', compact('kelasmahasiswa'));
}
}