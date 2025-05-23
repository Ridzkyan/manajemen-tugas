<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $materis = Materi::where('kelas_id', $kelasId)->paginate(10); // <-- pakai PAGINATE sekarang

        return view('dosen.kelas.materi.index', compact('kelas', 'materis'));
    }

    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:pdf,link',
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'link' => 'nullable|url'
        ]);

        // Validasi manual
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

        Materi::create([
            'kelas_id' => $kelasId,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'file' => $filePath,
            'link' => $request->link,
        ]);

        return redirect()->back()->with('success', 'Materi berhasil diupload!');
    }

    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);

        if ($materi->tipe == 'pdf' && $materi->file) {
            Storage::disk('public')->delete($materi->file);
        }

        $materi->delete();

        return redirect()->back()->with('success', 'Materi berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $materiIds = $request->materi_ids;

        if ($materiIds) {
            $materis = Materi::whereIn('id', $materiIds)->get();

            foreach ($materis as $materi) {
                if ($materi->tipe === 'pdf' && $materi->file) {
                    Storage::disk('public')->delete($materi->file);
                }
                $materi->delete();
            }
        }

        return redirect()->back()->with('success', 'Materi yang dipilih berhasil dihapus!');
    }
}
