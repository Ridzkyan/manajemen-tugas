<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Kelas\Kelas;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index($kelasId, Request $request)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $tipe = $request->query('tipe', 'tugas');
        $daftarTugas = Tugas::where('kelas_id', $kelasId)
            ->where('tipe', $tipe)
            ->get();

        $pengumpulanTugas = PengumpulanTugas::where('mahasiswa_id', auth()->id())
            ->whereIn('tugas_id', $daftarTugas->pluck('id'))
            ->get()
            ->keyBy('tugas_id');

        return view('mahasiswa.kelas.tugas.index', compact('kelas', 'daftarTugas', 'tipe', 'pengumpulanTugas'));
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
    // Debug information
    \Log::info('Upload attempt', [
        'method' => $request->method(),
        'kelas_id' => $kelasId,
        'tugas_id' => $tugasId,
        'user_id' => auth()->id(),
        'has_file' => $request->hasFile('file_tugas'),
        'all_files' => $request->allFiles(),
        'content_type' => $request->header('Content-Type'),
        'file_size' => $request->hasFile('file_tugas') ? $request->file('file_tugas')->getSize() : 'no file'
    ]);

    try {
        // Cek apakah tugas ada
        $tugas = Tugas::where('id', $tugasId)
                     ->where('kelas_id', $kelasId)
                     ->first();
        
        if (!$tugas) {
            \Log::error('Tugas tidak ditemukan', ['tugas_id' => $tugasId, 'kelas_id' => $kelasId]);
            return redirect()->back()->with('tugas_error', 'Tugas tidak ditemukan.');
        }

        // Cek deadline
        if ($tugas->deadline && now()->gt(Carbon::parse($tugas->deadline))) {
            \Log::warning('Deadline sudah lewat');
            return redirect()->back()->with('tugas_error', 'Maaf, waktu pengumpulan sudah berakhir.');
        }

        // Cek apakah ada file
        if (!$request->hasFile('file_tugas')) {
            \Log::error('Tidak ada file dalam request');
            return redirect()->back()->with('tugas_error', 'File tidak ditemukan. Pastikan Anda memilih file.');
        }

        $file = $request->file('file_tugas');
        
        // Log detail file
        \Log::info('File details', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'is_valid' => $file->isValid(),
            'error' => $file->getError()
        ]);

        // Validasi manual
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
        $fileExtension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            \Log::error('Format file tidak diperbolehkan', ['extension' => $fileExtension]);
            return redirect()->back()->with('tugas_error', 'Format file tidak diperbolehkan. Gunakan: PDF, DOC, DOCX, atau TXT.');
        }

        // Cek ukuran file (10MB = 10485760 bytes)
        if ($file->getSize() > 10485760) {
            \Log::error('File terlalu besar', ['size' => $file->getSize()]);
            return redirect()->back()->with('tugas_error', 'File terlalu besar. Maksimal 10MB.');
        }

        // Buat nama file unik
        $namaFile = "jawaban_{$tugasId}_" . auth()->id() . "_" . time() . ".{$fileExtension}";
        
        // Pastikan direktori ada
        $directory = 'jawaban_tugas';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
            \Log::info('Direktori dibuat', ['directory' => $directory]);
        }

        // Simpan file
        $path = $file->storeAs($directory, $namaFile, 'public');
        
        if (!$path) {
            \Log::error('Gagal menyimpan file ke storage');
            return redirect()->back()->with('tugas_error', 'Gagal menyimpan file. Silakan coba lagi.');
        }

        \Log::info('File berhasil disimpan', ['path' => $path]);

        // Simpan ke database
        $pengumpulan = PengumpulanTugas::updateOrCreate(
            [
                'mahasiswa_id' => auth()->id(),
                'tugas_id' => $tugasId,
            ],
            [
                'file_jawaban' => $path,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \Log::info('Data berhasil disimpan ke database', ['pengumpulan_id' => $pengumpulan->id]);

        return redirect()->back()->with('tugas_success', 'Tugas berhasil dikumpulkan!');

    } catch (\Exception $e) {
        \Log::error('Upload gagal dengan exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()->with('tugas_error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function preview($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where([
            'mahasiswa_id' => auth()->id(),
            'tugas_id' => $tugasId,
        ])->firstOrFail();

        if (!$pengumpulan->file_jawaban || !Storage::disk('public')->exists($pengumpulan->file_jawaban)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($pengumpulan->file_jawaban);
    }

    public function delete($kelasId, $tugasId)
    {
        try {
            $pengumpulan = PengumpulanTugas::where([
                'mahasiswa_id' => auth()->id(),
                'tugas_id' => $tugasId,
            ])->firstOrFail();

            if ($pengumpulan->file_jawaban && Storage::disk('public')->exists($pengumpulan->file_jawaban)) {
                Storage::disk('public')->delete($pengumpulan->file_jawaban);
            }

            $pengumpulan->delete();

            return redirect()->back()->with('tugas_success', 'File tugas berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Delete tugas gagal', ['error' => $e->getMessage()]);
            return back()->with('tugas_error', 'Gagal menghapus file tugas.');
        }
    }
}