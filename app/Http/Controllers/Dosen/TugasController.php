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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapNilaiExport;
use App\Notifications\TugasBaruNotification;
use App\Notifications\TugasDinilaiNotification;

class TugasController extends Controller
{
    public function pilihKelas()
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)->with('tugas')->get();

        foreach ($kelasList as $kelas) {
            $kelas->deadline_terdekat = $kelas->tugas
                ->whereNotNull('deadline')
                ->sortBy('deadline')
                ->first()
                ->deadline ?? null;
        }

        $kelasGrouped = $kelasList->groupBy(function ($kelas) {
            return strtoupper(substr($kelas->nama_kelas, 0, 1));
        })->sortKeys();

        return view('dosen.tugas_ujian.pilih_kelas', compact('kelasGrouped'));
    }

    public function index($kelasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        $allKelas = Kelas::where('dosen_id', Auth::id())->get();
        $kelasGrouped = $allKelas->groupBy('nama_kelas')->sortKeys();
        $kategoriList = $allKelas->pluck('nama_kelas')->unique()->sort();

        return view('dosen.tugas_ujian.index', compact('kelas', 'tugas', 'kelasGrouped', 'kategoriList'));
    }

    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:tugas,ujian',
            'deskripsi' => 'nullable|string',
            'file_soal' => 'nullable|file|mimes:pdf,docx,doc|max:5120',
            'deadline' => 'nullable|date_format:Y-m-d\TH:i'
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

        if ($request->ajax()) {
            return response()->json(['message' => 'Tugas berhasil ditambahkan!'], 200);
        }

        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mahasiswa) {
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new TugasBaruNotification($tugas));
            }
        }

        return redirect()->route('dosen.tugas_ujian.index', $kelasId)->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function edit($kelasId, $tugasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas = Tugas::findOrFail($tugasId);

        return view('dosen.kelola_kelas.edit', compact('kelas', 'tugas'));
    }

    public function update(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:tugas,ujian',
            'deskripsi' => 'nullable|string',
            'file_soal' => 'nullable|file|mimes:pdf,docx,doc|max:5120',
            'deadline' => 'nullable|date_format:Y-m-d\TH:i'
        ]);

        $tugas = Tugas::findOrFail($tugasId);

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
        $tugas = Tugas::findOrFail($tugasId);

        if ($mahasiswa->hasVerifiedEmail()) {
            $mahasiswa->notify(new TugasDinilaiNotification($tugas, $pengumpulan));
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan dan notifikasi dikirim!');
    }

     public function rekapNilai(Request $request)
    {
        $dosenId = Auth::id();
        $kelasList = Kelas::where('dosen_id', $dosenId)->get();

        $selectedKelasId = $request->kelas_id;

        $tugas = $selectedKelasId
            ? Tugas::with(['pengumpulanTugas.mahasiswa', 'kelas'])
                ->where('kelas_id', $selectedKelasId)
                ->get()
            : collect();

        return view('dosen.rekap_nilai.rekap', compact('kelasList', 'tugas', 'selectedKelasId'));
    }

    public function rekapPerKelas($kelasId)
    {
        $kelas = Kelas::with(['tugas.pengumpulanTugas' => function ($q) {
            $q->with('mahasiswa');
        }])->where('dosen_id', Auth::id())->findOrFail($kelasId);

        $tugas = $kelas->tugas;

        return view('dosen.rekap_nilai.rekap_detail', compact('kelas', 'tugas'));
    }

    public function exportRekap($kelasId)
    {
        $export = new RekapNilaiExport($kelasId);
        return Excel::download($export, 'rekap_nilai_kelas_'.$kelasId.'.xlsx');
    }
}