<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NilaiMahasiswaExport;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        return view('dosen.kelas.tugas.index', compact('kelas', 'tugas'));
    }

    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:tugas,ujian',
            'deskripsi' => 'nullable|string',
            'file_soal' => 'nullable|file|mimes:pdf,docx,doc|max:5120',
            'deadline' => 'nullable|date'
        ]);

        $filePath = null;
        if ($request->hasFile('file_soal')) {
            $filePath = $request->file('file_soal')->store('tugas', 'public');
        }

        Tugas::create([
            'kelas_id' => $kelasId,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
            'deadline' => $request->deadline,
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan!');
    }

    // Menampilkan halaman penilaian tugas
    public function penilaian($kelasId, $tugasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::findOrFail($tugasId);

        $mahasiswa = $kelas->mahasiswa;
        return view('dosen.kelas.tugas.penilaian', compact('kelas', 'tugas', 'mahasiswa'));
    }

    // Menyimpan nilai dan feedback tugas
    public function nilaiTugas(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',  // Nilai tugas antara 0-100
            'feedback' => 'nullable|string',
        ]);

        $tugas = Tugas::findOrFail($tugasId);

            // Simpan nilai dan feedback
    $tugas->nilai = $request->nilai;
    $tugas->feedback = $request->feedback;
    $tugas->save();

    return redirect()->back()->with('success', 'Penilaian berhasil disimpan!');   
}

}
