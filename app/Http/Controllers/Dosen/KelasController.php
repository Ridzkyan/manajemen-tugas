<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\Materi;
use App\Models\Tugas\Tugas;
use App\Notifications\MateriBaruNotification;

class KelasController extends Controller
{
    public function index()
    {
    $kelas = Kelas::where('dosen_id', Auth::id())->get();

    $kelasGrouped = $kelas->groupBy('nama_kelas')->sortKeys();
    $kategoriList = $kelas->pluck('nama_kelas')->unique()->sort();

    return view('dosen.kelola_kelas.index', compact('kelasGrouped', 'kategoriList'));
    }


    public function create()
    {
        return view('dosen.kelola_kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'nama_matakuliah' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url'
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'nama_matakuliah' => $request->nama_matakuliah,
            'kode_unik' => 'KLS-' . rand(10000, 99999),
            'dosen_id' => Auth::id(),
            'whatsapp_link' => $request->whatsapp_link,
        ]);

        return redirect()->route('dosen.kelola_kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

    public function show($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        $mahasiswa = $kelas->mahasiswa;

        return view('dosen.kelola_kelas.show', compact('kelas', 'mahasiswa'));
    }

    public function edit($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        return view('dosen.kelola_kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'nama_matakuliah' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url',
        ]);

        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'nama_matakuliah' => $request->nama_matakuliah,
            'whatsapp_link' => $request->whatsapp_link,
        ]);

        return redirect()->route('dosen.dashboard')->with('success', 'Kelas berhasil diupdate!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        $kelas->delete();

        return redirect()->route('dosen.kelola_kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }

    public function manage($id)
    {
        $kelas = Kelas::with('materi')->where('dosen_id', Auth::id())->where('id', $id)->firstOrFail();
        $allKelas = Kelas::where('dosen_id', Auth::id())->get();

        return view('dosen.kelola_kelas.index', compact('kelas', 'allKelas'));
    }

    public function uploadMateri(Request $request, $kelasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:pdf,link',
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'link' => 'nullable|url'
        ]);

        if ($request->tipe === 'pdf' && !$request->hasFile('file')) {
            return back()->with('error', 'File PDF harus diupload.');
        }

        if ($request->tipe === 'link' && !$request->link) {
            return back()->with('error', 'Link YouTube harus diisi.');
        }

        $filePath = null;
        if ($request->tipe === 'pdf' && $request->hasFile('file')) {
            $filePath = $request->file('file')->store('materi', 'public');
        }

        $materi = Materi::create([
            'kelas_id' => $kelasId,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'file' => $filePath,
            'link' => $request->link,
        ]);

        $kelas = Kelas::findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mhs) {
            if ($mhs->hasVerifiedEmail()) {
                $mhs->notify(new MateriBaruNotification($materi));
            }
        }

        return redirect()->back()->with('success', 'Materi berhasil diupload dan notifikasi dikirim!');
    }

   public function materiDanKelas()
{
    $kelasList = Kelas::with(['materi'])->withCount('mahasiswa')
        ->where('dosen_id', Auth::id())
        ->get();

    $kelasGrouped = $kelasList
        ->sortBy(function ($kelas) {
            return strtoupper(trim(substr($kelas->nama_kelas, -1)));
        })
        ->groupBy(function ($kelas) {
            return strtoupper(trim(substr($kelas->nama_kelas, -1)));
        });

    $kelasPertama = $kelasList->first();
    $kategoriList = $kelasGrouped->keys()->sort()->values();

    return view('dosen.materi_kelas.materi_dan_kelas', compact('kelasGrouped', 'kelasPertama', 'kategoriList'));
}


    public function uploadMateriGlobal(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'tipe' => 'required|in:pdf,link',
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'link' => 'nullable|url'
        ]);

        if ($request->tipe === 'pdf' && !$request->hasFile('file')) {
            return back()->with('error', 'File PDF harus diupload.');
        }

        if ($request->tipe === 'link' && !$request->link) {
            return back()->with('error', 'Link YouTube harus diisi.');
        }

        $filePath = null;
        if ($request->tipe === 'pdf' && $request->hasFile('file')) {
            $filePath = $request->file('file')->store('materi', 'public');
        }

        $materi = Materi::create([
            'kelas_id' => $request->kelas_id,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'file' => $filePath,
            'link' => $request->link,
            'status' => 'pending',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        foreach ($kelas->mahasiswa as $mhs) {
            if ($mhs->hasVerifiedEmail()) {
                $mhs->notify(new MateriBaruNotification($materi));
            }
        }

        return redirect()->back()->with('success', 'Materi berhasil diunggah dan notifikasi dikirim!');
    }

    public function detailMateri($id, $slug)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($id);
        $materis = $kelas->materi;

        return view('dosen.materi_kelas.detail_materi', compact('kelas', 'materis'));   
    }

    public function komunikasi()
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->get();

        
        $kelasGrouped = $kelas->sortBy(function ($item) {
            
            return strtoupper(trim(substr($item->nama_kelas, -1)));
        })->groupBy(function ($item) {
            return strtoupper(trim(substr($item->nama_kelas, -1)));
        });

        $kategoriList = $kelasGrouped->keys()->sort()->values()->all();

        return view('dosen.komunikasi.komunikasi', compact('kelasGrouped', 'kategoriList'));
    }
}