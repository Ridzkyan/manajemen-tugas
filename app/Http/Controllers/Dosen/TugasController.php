<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

        $tugas = Tugas::create([
            'kelas_id' => $kelasId,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
            'deadline' => $request->deadline,
            'status' => 'menunggu', // ⬅️ status default
        ]);

        // NOTIFIKASI TIDAK DIKIRIM SAAT BELUM DISETUJUI

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan dan menunggu persetujuan admin.');
    }

    public function penilaian($kelasId, $tugasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::findOrFail($tugasId);

        $mahasiswa = $kelas->mahasiswa;
        return view('dosen.kelas.tugas.penilaian', compact('kelas', 'tugas', 'mahasiswa'));
    }

    public function nilaiTugas(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $tugas = Tugas::findOrFail($tugasId);
        $tugas->nilai = $request->nilai;
        $tugas->feedback = $request->feedback;
        $tugas->save();

        // Notifikasi saat dinilai tetap dikirim
        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mahasiswa) {
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new \App\Notifications\TugasDinilaiNotification($tugas));
            }
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan dan notifikasi dikirim!');
    }
}
