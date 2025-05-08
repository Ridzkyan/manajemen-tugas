<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Tugas;

class KontenController extends Controller
{
    public function index()
    {
        $materiTerbaru = Materi::with('kelas')->latest()->take(5)->get();
        $tugasTerbaru = Tugas::with('kelas')->latest()->take(5)->get();
    
        return view('admin.konten.index', compact('materiTerbaru', 'tugasTerbaru'));
    }
}
