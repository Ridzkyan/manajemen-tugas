<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas\Kelas;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        $pengumpulanTugas = PengumpulanTugas::where('mahasiswa_id', auth()->id())
            ->whereIn('tugas_id', $tugas->pluck('id'))
            ->pluck('tugas_id')
            ->toArray();

        return view('mahasiswa.kelas.tugas.index', compact('kelas', 'tugas', 'pengumpulanTugas'));
    }

    public function show($kelasId, $tugasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas = Tugas::where('id', $tugasId)->where('kelas_id', $kelasId)->firstOrFail();

        // Hapus where('kelas_id') karena kolomnya tidak ada di pengumpulan_tugas
        $sudahDikumpulkan = PengumpulanTugas::where([
            'mahasiswa_id' => auth()->id(),
            'tugas_id' => $tugasId,
        ])->exists();

        return view('mahasiswa.kelas.tugas.show', compact('kelas', 'tugas', 'sudahDikumpulkan'));
    }

    public function upload(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        if (!$request->hasFile('file_tugas')) {
            return response()->json(['message' => 'Tidak ada file yang diunggah.'], 422);
        }

        try {
            $file = $request->file('file_tugas');
            $namaFile = 'tugas_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tugas_upload', $namaFile, 'public');

            PengumpulanTugas::updateOrCreate(
                [
                    'mahasiswa_id' => auth()->id(),
                    'tugas_id' => $tugasId,
                ],
                ['file' => $path]
            );

            return response()->json(['message' => 'Berhasil mengunggah file.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengunggah file.'], 500);
        }
    }

    public function preview($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where([
            'mahasiswa_id' => auth()->id(),
            'tugas_id' => $tugasId,
        ])->firstOrFail();

        if (!$pengumpulan->file || !Storage::disk('public')->exists($pengumpulan->file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($pengumpulan->file);
    }

    public function delete($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where([
            'mahasiswa_id' => auth()->id(),
            'tugas_id' => $tugasId,
        ])->firstOrFail();

        if ($pengumpulan->file && Storage::disk('public')->exists($pengumpulan->file)) {
            Storage::disk('public')->delete($pengumpulan->file);
        }

        $pengumpulan->delete();

        return redirect()->back()->with('success', 'File tugas berhasil dihapus.');
    }
}
