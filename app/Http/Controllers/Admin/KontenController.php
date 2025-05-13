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
        $matkulList = \App\Models\Kelas::select('nama_matakuliah')->distinct()->get();
    
        return view('admin.konten.index', [
            'materiTerbaru' => $materiTerbaru,
            'tugasTerbaru' => $tugasTerbaru,
            'matkulList' => $matkulList,
        ]);

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
