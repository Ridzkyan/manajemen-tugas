<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;
use App\Models\Kelas\Kelas;
use App\Models\User\Mahasiswa;
use App\Notifications\TugasBaruNotification;
use App\Notifications\TugasDinilaiNotification;
use App\Exports\RekapNilaiExport;
use Maatwebsite\Excel\Facades\Excel;

class TugasController extends Controller
{
    public function pilihKelas()
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)->get();

        return view('dosen.tugas_ujian.pilih_kelas', compact('kelasList'));
    }

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

        $filePath = $request->hasFile('file_soal') 
            ? $request->file('file_soal')->store('tugas', 'public')
            : null;

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

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan dan menunggu persetujuan admin.');
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
        $tugas = Tugas::with('kelas')->findOrFail($tugasId);

        $pengumpul = PengumpulanTugas::with('mahasiswa')
            ->where('tugas_id', $tugasId)
            ->get();

        return view('dosen.tugas_ujian.penilaian', compact('kelas', 'tugas', 'pengumpul'));
    }

    public function nilaiTugas(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->firstOrFail();

        $pengumpulan->nilai = $request->nilai;
        $pengumpulan->feedback = $request->feedback;
        $pengumpulan->save();

        $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
        if ($mahasiswa->hasVerifiedEmail()) {
            $tugas = Tugas::findOrFail($tugasId);
            $mahasiswa->notify(new TugasDinilaiNotification($tugas));
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan dan notifikasi dikirim!');
    }

    public function rekapNilai(Request $request)
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)->get();

        $selectedKelasId = $request->kelas_id;

        $tugas = $selectedKelasId
            ? Tugas::where('kelas_id', $selectedKelasId)->with('kelas')->get()
            : collect();

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
}
