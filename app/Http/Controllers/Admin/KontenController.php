<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas\Materi;
use App\Models\Tugas\Tugas;

class KontenController extends Controller
{
    public function index()
    {
        $materiTerbaru = Materi::with('kelas')->latest()->take(5)->get();
        $tugasTerbaru = Tugas::with('kelas')->latest()->take(5)->get();
    
        return view('admin.konten.index', compact('materiTerbaru', 'tugasTerbaru'));
    }

    public function setujuiMateri($id)
    {
        $materi = Materi::findOrFail($id);
        $materi->status = 'disetujui';
        $materi->save();
        return back()->with('success', 'Materi disetujui.');
    }

    public function tolakMateri($id)
    {
        $materi = Materi::findOrFail($id);
        $materi->status = 'ditolak';
        $materi->save();
        return back()->with('success', 'Materi ditolak.');
    }

    public function setujuiTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->status = 'disetujui';
        $tugas->save();
        return back()->with('success', 'Tugas disetujui.');
    }

    public function tolakTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->status = 'ditolak';
        $tugas->save();
        return back()->with('success', 'Tugas ditolak.');
    }
}
