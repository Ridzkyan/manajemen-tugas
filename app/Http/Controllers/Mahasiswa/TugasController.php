<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas= Tugas::where('kelas_id', $kelasId)
            ->where('status', 'disetujui')
            ->get();
    

        // Ambil daftar tugas yang sudah dikumpulkan oleh mahasiswa ini
        $pengumpulanTugas = PengumpulanTugas::where('mahasiswa_id', auth()->id())
                            ->where('kelas_id', $kelasId)
                            ->pluck('tugas_id')
                            ->toArray();

        return view('mahasiswa.kelas.tugas.index', compact('kelas', 'tugas', 'pengumpulanTugas'));
    }

    public function upload(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf,doc,docx,zip|max:10240'
        ]);

        $file = $request->file('file_tugas');
        $namaFile = 'tugas_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tugas_upload', $namaFile, 'public');

        // Cek apakah sudah ada sebelumnya
        $existing = PengumpulanTugas::where('mahasiswa_id', auth()->id())
                    ->where('kelas_id', $kelasId)
                    ->where('tugas_id', $tugasId)
                    ->first();

        if ($existing) {
            // Update file lama
            if ($existing->file && Storage::disk('public')->exists($existing->file)) {
                Storage::disk('public')->delete($existing->file);
            }
            $existing->update([
                'file' => $path
            ]);
        } else {
            // Simpan baru
            PengumpulanTugas::create([
                'mahasiswa_id' => auth()->id(),
                'kelas_id' => $kelasId,
                'tugas_id' => $tugasId,
                'file' => $path
            ]);
        }
        if ($request->ajax()) {
            return response()->json(['message' => 'Tugas berhasil diunggah.']);
        }

        return redirect()->back()->with('success', 'Tugas berhasil diunggah.');
    }

    public function preview($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where('mahasiswa_id', auth()->id())
            ->where('kelas_id', $kelasId)
            ->where('tugas_id', $tugasId)
            ->firstOrFail();

        if (!$pengumpulan->file || !Storage::disk('public')->exists($pengumpulan->file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($pengumpulan->file);
    }

    public function delete($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where('mahasiswa_id', auth()->id())
            ->where('kelas_id', $kelasId)
            ->where('tugas_id', $tugasId)
            ->firstOrFail();

        // Hapus file fisik
        if ($pengumpulan->file && Storage::disk('public')->exists($pengumpulan->file)) {
            Storage::disk('public')->delete($pengumpulan->file);
        }

        // Hapus record
        $pengumpulan->delete();

        return redirect()->back()->with('success', 'File tugas berhasil dihapus.');
    }
    
}
