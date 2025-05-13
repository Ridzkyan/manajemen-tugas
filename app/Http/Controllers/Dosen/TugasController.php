<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TugasBaruNotification;
use App\Notifications\TugasDinilaiNotification;
use App\Exports\RekapNilaiExport;
use Maatwebsite\Excel\Facades\Excel;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        return view('dosen.tugas_ujian.index', compact('kelas', 'tugas'));
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
        ]);

        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mahasiswa) {
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new TugasBaruNotification($tugas));
            }
        }

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan dan notifikasi dikirim!');
    }

    public function detail($kelasId)
    {
        $kelas = Kelas::with('tugas')->where('id', $kelasId)->where('dosen_id', Auth::id())->firstOrFail();
        $tugas = $kelas->tugas;

        return view('dosen.tugas_ujian.detail_tugas', compact('kelas', 'tugas'));
    }

    public function penilaian($kelasId, $tugasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::findOrFail($tugasId);

        $mahasiswa = $kelas->mahasiswa;
        return view('dosen.tugas_ujian.penilaian', compact('kelas', 'tugas', 'mahasiswa'));
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

        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mahasiswa) {
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new TugasDinilaiNotification($tugas));
            }
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan dan notifikasi dikirim!');
    }

    public function rekapNilai(Request $request)
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)->get();

        $selectedKelasId = $request->kelas_id;

        $tugas = collect(); // default kosong
        if ($selectedKelasId) {
            $tugas = Tugas::where('kelas_id', $selectedKelasId)
                ->with('kelas')
                ->get();
        }

        return view('dosen.rekap_nilai.rekap', compact('kelasList', 'tugas', 'selectedKelasId'));
    }

    public function rekapPerKelas($kelasId)
    {
        $kelas = Kelas::with('tugas')->where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = $kelas->tugas;

        return view('dosen.rekap_nilai.rekap_detail', compact('kelas', 'tugas'));
    }

    public function exportRekap($kelasId)
    {
        return Excel::download(new RekapNilaiExport($kelasId), 'rekap_nilai_kelas_' . $kelasId . '.xlsx');
    }

    public function pilihKelas()
    {
    $dosenId = Auth::id();
    $kelasList = Kelas::where('dosen_id', $dosenId)->get();
    return view('dosen.tugas_ujian.pilih_kelas', compact('kelasList'));
    }


}
