<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;

class UjianController extends Controller
{
    public function index($kelasId)
    {
    $kelas = \App\Models\Kelas\Kelas::findOrFail($kelasId); // Ambil data kelas
    $ujians = \App\Models\Tugas\Tugas::where('kelas_id', $kelasId)
                ->where('tipe', 'ujian')
                ->get();

    return view('mahasiswa.kelas.ujian.index', compact('kelas', 'ujians', 'kelasId'));
    }


    public function create($kelasId)
    {
        return view('mahasiswa.ujian.create', compact('kelasId'));
    }

    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date'
        ]);

        Ujian::create([
            'kelas_id' => $kelasId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('mahasiswa.ujian.index', $kelasId)->with('success', 'Ujian berhasil ditambahkan.');
    }

    public function show($kelasId, $id)
    {
        $ujian = Ujian::where('id', $id)->where('kelas_id', $kelasId)->firstOrFail();
        return view('mahasiswa.ujian.show', compact('kelasId', 'ujian'));
    }

    public function edit($kelasId, $id)
    {
        $ujian = Ujian::where('id', $id)->where('kelas_id', $kelasId)->firstOrFail();
        return view('mahasiswa.ujian.edit', compact('kelasId', 'ujian'));
    }

    public function update(Request $request, $kelasId, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date'
        ]);

        $ujian = Ujian::where('id', $id)->where('kelas_id', $kelasId)->firstOrFail();

        $ujian->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('mahasiswa.ujian.index', $kelasId)->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy($kelasId, $id)
    {
        $ujian = Ujian::where('id', $id)->where('kelas_id', $kelasId)->firstOrFail();
        $ujian->delete();

        return redirect()->route('mahasiswa.ujian.index', $kelasId)->with('success', 'Ujian berhasil dihapus.');
    }

    public function kerjakan($kelasId, $id)
    {
        $ujian = \App\Models\Tugas\Tugas::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('tipe', 'ujian')
            ->firstOrFail();

        return view('mahasiswa.kelas.ujian.kerjakan', compact('ujian', 'kelasId'));
    }
}
