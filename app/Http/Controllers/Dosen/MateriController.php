<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Materi;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::with('materi')
            ->where('dosen_id', auth()->id())
            ->get();

        // Grouping berdasarkan huruf terakhir nama_kelas (misal: A, B, C)
        $kelasGrouped = $kelasList->groupBy(function ($kls) {
            return strtoupper(substr($kls->nama_kelas, -1));
        });

        return view('dosen.materi_kelas.materi_dan_kelas', compact('kelasGrouped'));
    }
    public function materiDanKelas()
    {
        $kelasList = Kelas::with('materi')
            ->where('dosen_id', Auth::id())
            ->get();

        $kelasGrouped = $kelasList->groupBy(function ($kelas) {
            return strtoupper(substr($kelas->nama_kelas, 0, 1));
        })->sortKeys();

        return view('dosen.materi_kelas.materi_dan_kelas', compact('kelasGrouped'));
    }
    public function edit($id)
    {
        $materi = Materi::findOrFail($id);
        return view('dosen.materi_kelas.edit', compact('materi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:pdf,link',
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'link' => 'nullable|url'
        ]);

        $materi = Materi::findOrFail($id);
        $materi->judul = $request->judul;
        $materi->tipe = $request->tipe;

        if ($request->tipe === 'link') {
            $materi->link = $request->link;
            if ($materi->file) {
                Storage::delete('public/' . $materi->file);
                $materi->file = null;
            }
        } elseif ($request->hasFile('file')) {
            if ($materi->file) {
                Storage::delete('public/' . $materi->file);
            }
            $materi->file = $request->file('file')->store('materi', 'public');
            $materi->link = null;
        }

        $materi->save();

        return back()->with('success', 'Materi berhasil diperbarui.');
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
            'status' => 'menunggu', // Set status default ke menunggu
        ]);

        $materi->load('kelas');

        // ===== Kirim Notifikasi ke Mahasiswa =====
        $kelas = Kelas::findOrFail($kelasId);
        $mahasiswas = $kelas->mahasiswas; // relasi di model Kelas

        foreach ($mahasiswas as $mhs) {
            if ($mhs->hasVerifiedEmail()) {
                $mhs->notify(new MateriBaruNotification($materi));
            }
        }

        return redirect()->back()->with('success', 'Materi berhasil diupload dan notifikasi dikirim dan menunggu persetujuan admin.');
    }

    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);

        if ($materi->file) {
            Storage::delete('public/' . $materi->file);
        }

        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }
}