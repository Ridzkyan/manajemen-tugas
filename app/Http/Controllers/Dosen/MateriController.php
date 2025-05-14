<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas\Materi;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
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
            'link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf|max:5120'
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