<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\TugasBaruNotification;
use App\Notifications\TugasDinilaiNotification;
use App\Exports\RekapNilaiExport;
use App\Notifications\TugasBaruNotification;

class TugasController extends Controller
{
    public function pilihKelas()
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)
            ->with('tugas') // pastikan eager loading relasi tugas
            ->get();

        // Tambahkan properti deadline_terdekat ke tiap kelas
        foreach ($kelasList as $kelas) {
            $kelas->deadline_terdekat = $kelas->tugas
                ->whereNotNull('deadline')
                ->sortBy('deadline')
                ->first()
                ->deadline ?? null;
        }

        // Kelompokkan berdasarkan huruf awal nama_kelas
        $kelasGrouped = $kelasList->groupBy(function ($kelas) {
            return strtoupper(substr($kelas->nama_kelas, 0, 1));
        })->sortKeys();

        return view('dosen.tugas_ujian.pilih_kelas', compact('kelasGrouped'));
    }

    public function index($kelasId)
{
    $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
    $tugas = Tugas::where('kelas_id', $kelasId)->get();

    // Ambil semua kelas dosen lalu group by kategori kelas
    $allKelas = Kelas::where('dosen_id', Auth::id())->get();
    $kelasGrouped = $allKelas->groupBy('nama_kelas')->sortKeys(); // atau nama lain: kategori
    $kategoriList = $allKelas->pluck('nama_kelas')->unique()->sort();

    return view('dosen.tugas_ujian.index', compact('kelas', 'tugas', 'kelasGrouped', 'kategoriList'));
}



    // Store Task
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

        // 📦 TANGGAPI SESUAI JENIS REQUEST
    if ($request->ajax()) {
        return response()->json(['message' => 'Tugas berhasil ditambahkan!'], 200);
    }

        // Notify students
        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mahasiswa) {
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new TugasBaruNotification($tugas));
            }
        }

        return redirect()->route('dosen.tugas_ujian.index', $kelasId)->with('success', 'Tugas berhasil ditambahkan!');
    }

    // Menampilkan form untuk mengedit tugas
    public function edit($kelasId, $tugasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas = Tugas::findOrFail($tugasId);

        return view('dosen.kelola_kelas.edit', compact('kelas', 'tugas'));
    }

    // Update Task
    public function update(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:tugas,ujian',
            'deskripsi' => 'nullable|string',
            'file_soal' => 'nullable|file|mimes:pdf,docx,doc|max:5120',
            'deadline' => 'nullable|date'
        ]);

        $tugas = Tugas::findOrFail($tugasId);

        // Handle file upload if there is a new file
        $filePath = $request->hasFile('file_soal') 
            ? $request->file('file_soal')->store('tugas', 'public')
            : $tugas->file_soal;

        $tugas->update([
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('dosen.tugas_ujian.index', $kelasId)->with('success', 'Tugas berhasil diupdate!');
    }

    // Delete Task
    public function destroy($kelasId, $tugasId)
    {
        $tugas = Tugas::findOrFail($tugasId);
        $tugas->delete();

        return redirect()->route('dosen.tugas_ujian.index', $kelasId)->with('success', 'Tugas berhasil dihapus!');
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