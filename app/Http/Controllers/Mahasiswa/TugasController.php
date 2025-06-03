<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas\Kelas;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        $pengumpulanTugas = PengumpulanTugas::where('mahasiswa_id', auth()->id())
            ->whereIn('tugas_id', $tugas->pluck('id'))
            ->get()
            ->keyBy('tugas_id');

        return view('mahasiswa.kelas.tugas.index', compact('kelas', 'tugas', 'pengumpulanTugas'));
    }

    public function show($kelasId, $tugasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $tugas = Tugas::where('id', $tugasId)->where('kelas_id', $kelasId)->firstOrFail();

        $pengumpulan = PengumpulanTugas::where([
            'mahasiswa_id' => auth()->id(),
            'tugas_id' => $tugasId,
        ])->first();

        $sudahDikumpulkan = !is_null($pengumpulan);

        $isDeadlineOver = $tugas->deadline ? Carbon::now()->gt(Carbon::parse($tugas->deadline)) : false;

        return view('mahasiswa.kelas.tugas.show', compact('kelas', 'tugas', 'sudahDikumpulkan', 'pengumpulan', 'isDeadlineOver'));
    }

    public function upload(Request $request, $kelasId, $tugasId)
    {
        $tugas = Tugas::findOrFail($tugasId);

        // Validasi deadline
        if ($tugas->deadline && now()->gt(Carbon::parse($tugas->deadline))) {
            return redirect()->back()->with('error', 'Maaf, waktu pengumpulan sudah berakhir.');
        }

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

            return redirect()->route('mahasiswa.kelas.tugas.index', $kelasId)
                ->with('success', 'Tugas berhasil dikumpulkan!');
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
