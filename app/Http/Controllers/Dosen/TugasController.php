<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tugas\Tugas;
use App\Models\Tugas\PengumpulanTugas;
use App\Models\Kelas\Kelas;
use App\Models\User\Mahasiswa;
use App\Notifications\TugasBaruNotification;
use App\Notifications\TugasDinilaiNotification;
use PhpOffice\PhpWord\IOFactory;
use Carbon\Carbon;
class TugasController extends Controller
{
    // Flask API Configuration
    private $flaskApiUrl = 'http://127.0.0.1:5000/api/similarity';
    private $apiTimeout = 30; // seconds

    public function index($kelasId)
    {
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        $tugas = Tugas::where('kelas_id', $kelasId)->get();

        return view('dosen.tugas_ujian.index', compact('kelas', 'tugas'));
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

    public function store(Request $request, $kelasId)
    {
        // Debugging untuk file upload
        Log::info('File Soal:', [$request->file('file_soal')]);
        Log::info('File Kunci:', [$request->file('file_kunci')]);

        // Validasi untuk file DOCX
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:tugas,ujian',
            'deskripsi' => 'nullable|string',
            'deadline' => 'nullable|date',
            'file_soal' => [
                'required',
                'file',
                'max:5120', // 5MB
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $extension = strtolower($value->getClientOriginalExtension());
                        $mimeType = $value->getMimeType();
                        
                        // Cek ekstensi file
                        if ($extension !== 'docx') {
                            $fail('File soal harus berupa file .docx');
                            return;
                        }
                        
                        // Array MIME types yang valid untuk file DOCX
                        $validMimeTypes = [
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/octet-stream',
                            'application/zip',
                        ];
                        
                        if (!in_array($mimeType, $validMimeTypes)) {
                            $fail('File soal harus berupa file .docx yang valid');
                        }
                    }
                }
            ],
            'file_kunci' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $extension = strtolower($value->getClientOriginalExtension());
                        $mimeType = $value->getMimeType();
                        
                        if ($extension !== 'docx') {
                            $fail('File kunci jawaban harus berupa file .docx');
                            return;
                        }
                        
                        $validMimeTypes = [
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/octet-stream',
                            'application/zip',
                        ];
                        
                        if (!in_array($mimeType, $validMimeTypes)) {
                            $fail('File kunci jawaban harus berupa file .docx yang valid');
                        }
                    }
                }
            ]
        ]);

        // Validasi file dengan PhpWord
        if (!$this->validateDocxFile($request->file('file_soal'))) {
            return response()->json([
                'success' => false,
                'message' => 'File soal tidak dapat dibaca. Pastikan file adalah .docx yang valid.',
                'errors' => ['file_soal' => ['File soal tidak valid atau rusak']]
            ], 422);
        }

        if ($request->hasFile('file_kunci') && !$this->validateDocxFile($request->file('file_kunci'))) {
            return response()->json([
                'success' => false,
                'message' => 'File kunci jawaban tidak dapat dibaca. Pastikan file adalah .docx yang valid.',
                'errors' => ['file_kunci' => ['File kunci jawaban tidak valid atau rusak']]
            ], 422);
        }

        // Menyimpan data tugas ke database
        $tugas = new Tugas();
        $tugas->kelas_id = $kelasId;
        $tugas->judul = $request->judul;
        $tugas->tipe = $request->tipe;
        $tugas->deskripsi = $request->deskripsi;
        $tugas->deadline = $request->deadline;
        $tugas->save();

        // Menyimpan file soal
        $soalName = "soal_tugas_{$tugas->id}.docx";
        $pathSoal = $request->file('file_soal')->storeAs('tugas', $soalName, 'public');
        $tugas->file_soal = $pathSoal;

        // Menyimpan file kunci (jika ada)
        if ($request->hasFile('file_kunci')) {
            $kunciName = "kunci_tugas_{$tugas->id}.docx";
            $pathKunci = $request->file('file_kunci')->storeAs('kunci_tugas', $kunciName, 'public');
            $tugas->file_kunci = $pathKunci;
        }

        $tugas->save();

        // Notifikasi ke mahasiswa
        $kelas = Kelas::with('mahasiswa')->findOrFail($kelasId);
        foreach ($kelas->mahasiswa as $mhs) {
            if ($mhs->hasVerifiedEmail()) {
                $mhs->notify(new TugasBaruNotification($tugas));
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Menilai tugas individual menggunakan Flask API
     */
    public function nilaiTugas(Request $request, $kelasId, $tugasId)
{
    $request->validate([
        'mahasiswa_id' => 'required|exists:users,id',
        'feedback' => 'nullable|string',
    ]);

    try {
        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->firstOrFail();

        $tugas = Tugas::findOrFail($tugasId);

        // Validasi file kunci jawaban
        if (!$tugas->file_kunci) {
            return redirect()->back()->with('error', 'File kunci jawaban tidak tersedia untuk tugas ini.');
        }

        // Path file dengan validasi yang lebih baik
        $jawabanPath = $this->getValidFilePath($pengumpulan->file);
        $kunciPath = storage_path('app/public/' . $tugas->file_kunci);

        // Validasi keberadaan file
        if (!$jawabanPath || !file_exists($jawabanPath)) {
            return redirect()->back()->with('error', 'File jawaban tidak ditemukan atau tidak dapat diakses.');
        }

        if (!file_exists($kunciPath)) {
            return redirect()->back()->with('error', 'File kunci jawaban tidak ditemukan.');
        }

        // Extract text dari dokumen
        $textJawaban = $this->extractTextFromDocx($jawabanPath);
        $textKunci = $this->extractTextFromDocx($kunciPath);

        if (empty($textJawaban) || empty($textKunci)) {
            return redirect()->back()->with('error', 'Gagal mengekstrak teks dari dokumen.');
        }

        // Panggil Flask API untuk similarity scoring
        $rawScore = $this->callSimilarityAPI($textJawaban, $textKunci);

        if ($rawScore !== null) {
            // PERBAIKAN: Gunakan convertScoreToFloat untuk mengkonversi nilai
            $score = $this->convertScoreToFloat($rawScore);
            
            Log::info("Raw score dari API: {$rawScore}, Converted score: {$score}");
            
            // Update nilai dan feedback
            $pengumpulan->nilai = $score;
            $pengumpulan->feedback = $request->feedback ?: $this->generateFeedback($score);
            $pengumpulan->dinilai_pada = now();
            $pengumpulan->save();

            // Kirim notifikasi ke mahasiswa
            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
            if ($mahasiswa->hasVerifiedEmail()) {
                $mahasiswa->notify(new TugasDinilaiNotification($tugas, $pengumpulan));
            }

            return redirect()->back()->with('success', "Nilai otomatis berhasil dihitung! Skor: {$score}");
        } else {
            return redirect()->back()->with('error', 'Gagal menghitung nilai otomatis. Periksa koneksi ke Flask API.');
        }

    } catch (\Exception $e) {
        Log::error('Error dalam nilaiTugas: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menilai tugas: ' . $e->getMessage());
    }
}

    /**
     * Menilai semua tugas sekaligus (batch scoring) - FIXED VERSION
     */
    // PERBAIKAN UNTUK METHOD nilaiBatch()
public function nilaiBatch($kelasId, $tugasId)
{
    try {
        $tugas = Tugas::findOrFail($tugasId);
        
        // Validasi file kunci jawaban
        if (!$tugas->file_kunci) {
            return redirect()->back()->with('error', 'File kunci jawaban tidak tersedia untuk tugas ini.');
        }

        $kunciPath = storage_path('app/public/' . $tugas->file_kunci);
        if (!file_exists($kunciPath)) {
            return redirect()->back()->with('error', 'File kunci jawaban tidak ditemukan.');
        }

        // Extract text dari kunci jawaban
        Log::info('Mengekstrak teks dari file: ' . $kunciPath);
        $textKunci = $this->extractTextFromDocx($kunciPath);
        if (empty($textKunci)) {
            return redirect()->back()->with('error', 'Gagal mengekstrak teks dari file kunci jawaban.');
        }
        Log::info('Berhasil mengekstrak teks, panjang: ' . strlen($textKunci) . ' karakter');

        // Ambil semua data yang belum dinilai
        $pengumpulList = PengumpulanTugas::with('mahasiswa')
            ->where('tugas_id', $tugasId)
            ->whereNull('nilai') // Hanya yang belum dinilai
            ->get()
            ->filter(function($pengumpulan) {
                $fileField = $this->getFileFieldName($pengumpulan);
                return !empty($pengumpulan->$fileField);
            });

        if ($pengumpulList->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada tugas dengan file jawaban yang valid untuk dinilai.');
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($pengumpulList as $pengumpulan) {
            try {
                $fileField = $this->getFileFieldName($pengumpulan);
                $fileName = $pengumpulan->$fileField;

                if (empty($fileName)) {
                    Log::warning("File kosong untuk mahasiswa: " . $pengumpulan->mahasiswa->nama . " (field: {$fileField})");
                    $errors[] = "File jawaban {$pengumpulan->mahasiswa->nama} kosong atau tidak ada";
                    $gagal++;
                    continue;
                }

                Log::info("Memproses pengumpulan ID: {$pengumpulan->id}, Mahasiswa: {$pengumpulan->mahasiswa->nama}, File: {$fileName} (field: {$fileField})");

                $jawabanPath = $this->getValidFilePath($fileName);
                
                Log::info('Memproses file jawaban: ' . ($jawabanPath ?: 'PATH NOT FOUND'));
                
                if (!$jawabanPath) {
                    Log::error("Path file jawaban tidak valid untuk: " . $fileName);
                    $errors[] = "Path file jawaban {$pengumpulan->mahasiswa->nama} tidak valid";
                    $gagal++;
                    continue;
                }

                if (!file_exists($jawabanPath)) {
                    Log::error("File jawaban tidak ditemukan: " . $jawabanPath);
                    $errors[] = "File jawaban {$pengumpulan->mahasiswa->nama} tidak ditemukan";
                    $gagal++;
                    continue;
                }

                // Validasi file sebelum ekstraksi
                if (!$this->isValidDocxFile($jawabanPath)) {
                    Log::error("File jawaban tidak valid atau corrupt: " . $jawabanPath);
                    $errors[] = "File jawaban {$pengumpulan->mahasiswa->nama} tidak valid atau corrupt";
                    $gagal++;
                    continue;
                }

                Log::info('Mengekstrak teks dari file: ' . $jawabanPath);
                $textJawaban = $this->extractTextFromDocx($jawabanPath);
                
                if (empty($textJawaban)) {
                    Log::error("Gagal mengekstrak teks dari: " . $jawabanPath);
                    $errors[] = "Gagal mengekstrak teks dari jawaban {$pengumpulan->mahasiswa->nama}";
                    $gagal++;
                    continue;
                }

                Log::info('Berhasil mengekstrak teks, panjang: ' . strlen($textJawaban) . ' karakter');

                // Panggil Flask API
                $rawScore = $this->callSimilarityAPI($textJawaban, $textKunci);

                if ($rawScore !== null) {
                    // PERBAIKAN: Gunakan method convertScoreToFloat untuk mengkonversi nilai
                    $score = $this->convertScoreToFloat($rawScore);
                    
                    Log::info("Raw score dari API: {$rawScore}, Converted score: {$score}");
                    
                    $pengumpulan->nilai = $score;
                    $pengumpulan->feedback = $this->generateFeedback($score);
                    $pengumpulan->dinilai_pada = now();
                    $pengumpulan->save();

                    // Kirim notifikasi
                    if ($pengumpulan->mahasiswa->hasVerifiedEmail()) {
                        $pengumpulan->mahasiswa->notify(new TugasDinilaiNotification($tugas, $pengumpulan));
                    }

                    $berhasil++;
                    Log::info("Berhasil menilai tugas: {$pengumpulan->mahasiswa->nama} - Score: {$score}");
                } else {
                    $errors[] = "Gagal menghitung skor untuk {$pengumpulan->mahasiswa->nama}";
                    $gagal++;
                }

            } catch (\Exception $e) {
                Log::error("Error menilai tugas mahasiswa {$pengumpulan->mahasiswa->nama}: " . $e->getMessage());
                $errors[] = "Error pada {$pengumpulan->mahasiswa->nama}: " . $e->getMessage();
                $gagal++;
            }
        }

        $message = "Berhasil menilai otomatis {$berhasil} tugas";
        if ($gagal > 0) {
            $message .= ", {$gagal} gagal";
        }

        if (!empty($errors)) {
            Log::warning('Batch scoring errors: ' . implode(', ', $errors));
        }

        return redirect()->back()->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Error dalam nilaiBatch: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menilai batch: ' . $e->getMessage());
    }
}



/**
 * Soft delete tugas (arsipkan)
 */
public function softDelete($tugasId)
{
    try {
        $tugas = TugasUjian::findOrFail($tugasId);
        
        // Pastikan dosen yang login adalah pemilik kelas
        if ($tugas->kelas->dosen_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk tugas ini.'
            ], 403);
        }
        
        $tugas->delete(); // Soft delete menggunakan SoftDeletes trait
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diarsipkan.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Permanent delete tugas
 */
public function permanentDelete($tugasId)
{
    try {
        $tugas = TugasUjian::withTrashed()->findOrFail($tugasId);
        
        // Pastikan dosen yang login adalah pemilik kelas
        if ($tugas->kelas->dosen_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk tugas ini.'
            ], 403);
        }
        
        // Hapus file terkait jika ada
        if ($tugas->file_soal) {
            Storage::delete('public/' . $tugas->file_soal);
        }
        
        if ($tugas->file_kunci) {
            Storage::delete('public/' . $tugas->file_kunci);
        }
        
        $tugas->forceDelete(); // Permanent delete
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus permanen.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Restore tugas dari arsip
 */
public function restore($tugasId)
{
    try {
        $tugas = TugasUjian::withTrashed()->findOrFail($tugasId);
        
        // Pastikan dosen yang login adalah pemilik kelas
        if ($tugas->kelas->dosen_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk tugas ini.'
            ], 403);
        }
        
        $tugas->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dipulihkan dari arsip.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Delete file (soal atau kunci jawaban)
 */
public function deleteFile(Request $request, $tugasId)
{
    try {
        $tugas = TugasUjian::findOrFail($tugasId);
        
        // Pastikan dosen yang login adalah pemilik kelas
        if ($tugas->kelas->dosen_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk tugas ini.'
            ], 403);
        }
        
        $fileType = $request->input('file_type');
        
        if ($fileType === 'soal' && $tugas->file_soal) {
            Storage::delete('public/' . $tugas->file_soal);
            $tugas->update(['file_soal' => null]);
            $message = 'File soal berhasil dihapus.';
        } elseif ($fileType === 'kunci' && $tugas->file_kunci) {
            Storage::delete('public/' . $tugas->file_kunci);
            $tugas->update(['file_kunci' => null]);
            $message = 'File kunci jawaban berhasil dihapus.';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Bulk delete tugas
 */
public function bulkDelete(Request $request)
{
    try {
        $tugasIds = $request->input('tugas_ids');
        
        if (empty($tugasIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tugas yang dipilih.'
            ], 400);
        }
        
        $tugas = TugasUjian::whereIn('id', $tugasIds)
            ->whereHas('kelas', function($query) {
                $query->where('dosen_id', auth()->id());
            })
            ->get();
        
        if ($tugas->count() !== count($tugasIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Beberapa tugas tidak ditemukan atau tidak memiliki akses.'
            ], 403);
        }
        
        foreach ($tugas as $t) {
            $t->delete(); // Soft delete
        }
        
        return response()->json([
            'success' => true,
            'message' => count($tugasIds) . ' tugas berhasil diarsipkan.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get info tugas
 */
public function getInfo($tugasId)
{
    try {
        $tugas = TugasUjian::withTrashed()->findOrFail($tugasId);
        
        // Pastikan dosen yang login adalah pemilik kelas
        if ($tugas->kelas->dosen_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk tugas ini.'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'tugas' => [
                'id' => $tugas->id,
                'judul' => $tugas->judul,
                'tipe' => $tugas->tipe,
                'deskripsi' => $tugas->deskripsi,
                'deadline' => $tugas->deadline,
                'file_soal' => $tugas->file_soal ? true : false,
                'file_kunci' => $tugas->file_kunci ? true : false,
                'created_at' => $tugas->created_at,
                'updated_at' => $tugas->updated_at,
                'deleted_at' => $tugas->deleted_at
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * PERBAIKAN: Konversi nilai dari API menjadi float yang benar
 * Contoh: 
 * - Jika API mengembalikan 0.8234 (dalam bentuk desimal 0-1) -> 82.34
 * - Jika API mengembalikan 82.34 (dalam bentuk persen) -> 82.34
 * - Jika API mengembalikan "82.34%" -> 82.34
 * - Jika API mengembalikan 8234 (dalam bentuk integer ribuan) -> 82.34
 */
private function convertScoreToFloat($rawScore)
{
    // Jika null atau kosong, return 0
    if ($rawScore === null || $rawScore === '') {
        Log::info('Raw score kosong, mengembalikan 0.0');
        return 0.0;
    }
    
    Log::info("Converting raw score: {$rawScore} (type: " . gettype($rawScore) . ")");
    
    // Jika berupa string, bersihkan dari karakter non-numerik
    if (is_string($rawScore)) {
        $cleanScore = str_replace(['%', ' '], '', $rawScore);
        $cleanScore = str_replace(',', '.', $cleanScore);
        $numericScore = (float) $cleanScore;
    } else {
        $numericScore = (float) $rawScore;
    }
    
    Log::info("Numeric score setelah cleaning: {$numericScore}");
    
    // Tentukan rentang nilai berdasarkan magnitude
    if ($numericScore >= 0 && $numericScore <= 1) {
        // Jika nilai dalam rentang 0-1 (decimal percentage), konversi ke 0-100
        $result = $numericScore * 100;
        Log::info("Detected decimal percentage (0-1), converting to percentage: {$result}");
    } elseif ($numericScore > 1 && $numericScore <= 100) {
        // Jika nilai dalam rentang 1-100 (normal percentage), gunakan langsung
        $result = $numericScore;
        Log::info("Detected normal percentage (1-100), using as is: {$result}");
    } elseif ($numericScore > 100 && $numericScore <= 10000) {
        // Jika nilai dalam rentang 100-10000 (integer percentage), bagi 100
        $result = $numericScore / 100;
        Log::info("Detected integer percentage (100-10000), dividing by 100: {$result}");
    } else {
        // Untuk nilai di luar rentang normal, gunakan logika default
        Log::warning("Score outside normal range: {$numericScore}, using as percentage");
        $result = $numericScore > 100 ? $numericScore / 100 : $numericScore;
    }
    
    // Pastikan nilai dalam rentang 0-100
    $result = max(0, min(100, $result));
    
    $finalResult = round($result, 2);
    Log::info("Final converted score: {$finalResult}");
    
    return $finalResult;
}


/**
 * PERBAIKAN: Metode getValidFilePath dengan logging yang lebih baik
 */
private function getValidFilePath($filename)
{
    // PERBAIKAN: Tambahkan validasi yang lebih ketat
    if (empty($filename) || is_null($filename)) {
        Log::error('Filename kosong atau null');
        return null;
    }

    // Trim whitespace dan cek lagi
    $filename = trim($filename);
    if (empty($filename)) {
        Log::error('Filename kosong setelah trim');
        return null;
    }

    Log::info("Mencari file dengan nama: '{$filename}'");

    // Kemungkinan lokasi file
    $possiblePaths = [
        // Path langsung dari database
        storage_path('app/public/' . $filename),
        
        // Path dengan folder jawaban_tugas
        storage_path('app/public/jawaban_tugas/' . $filename),
        
        // Path jika filename sudah termasuk folder
        storage_path('app/public/' . basename($filename)),
        
        // Path jika ada subfolder lain
        storage_path('app/public/uploads/' . $filename),
        storage_path('app/public/uploads/jawaban_tugas/' . $filename),
        
        // Path dengan nama file yang mungkin berubah
        storage_path('app/public/tugas_jawaban/' . $filename),
        storage_path('app/public/files/' . $filename),
    ];

    foreach ($possiblePaths as $path) {
        Log::info("Checking path: " . $path);
        if (file_exists($path) && is_readable($path)) {
            Log::info("File ditemukan dan dapat dibaca di: " . $path);
            return $path;
        }
    }

    // Jika tidak ditemukan, coba cari dengan pattern matching
    $searchPaths = [
        storage_path('app/public/'),
        storage_path('app/public/jawaban_tugas/'),
        storage_path('app/public/uploads/'),
        storage_path('app/public/tugas_jawaban/'),
        storage_path('app/public/files/'),
    ];

    foreach ($searchPaths as $searchPath) {
        if (is_dir($searchPath)) {
            Log::info("Mencari dengan pattern di: " . $searchPath);
            $files = glob($searchPath . '*' . basename($filename) . '*');
            if (!empty($files)) {
                $foundFile = $files[0];
                Log::info("File ditemukan dengan pattern matching: " . $foundFile);
                return $foundFile;
            }
        }
    }

    Log::error("File tidak ditemukan di semua lokasi yang dicek: '{$filename}'");
    
    // TAMBAHAN: Debug info untuk troubleshooting
    Log::info("Debug info - storage path: " . storage_path('app/public/'));
    Log::info("Debug info - public path exists: " . (is_dir(storage_path('app/public/')) ? 'Yes' : 'No'));
    
    return null;
}

/**
 * TAMBAHAN: Method untuk mendeteksi nama field file yang benar
 */
private function getFileFieldName($pengumpulan)
{
    // Kemungkinan nama field untuk file
    $possibleFields = [
        'file',
        'file_jawaban', 
        'path_file',
        'file_path',
        'jawaban_file',
        'document',
        'attachment'
    ];
    
    foreach ($possibleFields as $field) {
        if (isset($pengumpulan->$field)) {
            Log::info("Field file ditemukan: " . $field);
            return $field;
        }
    }
    
    // Default fallback
    Log::warning("Tidak ditemukan field file yang valid, menggunakan 'file' sebagai default");
    return 'file';
}

/**
 * TAMBAHAN: Method untuk debug struktur tabel
 */
public function debugTableStructure()
{
    try {
        // Ambil satu record untuk melihat struktur
        $sample = PengumpulanTugas::first();
        
        if ($sample) {
            $attributes = $sample->getAttributes();
            $columns = array_keys($attributes);
            
            Log::info("Kolom yang tersedia di tabel pengumpulan_tugas:");
            foreach ($columns as $column) {
                Log::info("- " . $column . ": " . gettype($attributes[$column]));
            }
            
            return response()->json([
                'success' => true,
                'columns' => $columns,
                'sample_data' => $attributes
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data di tabel pengumpulan_tugas'
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Error getting table structure: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
public function debugPengumpulan($kelasId, $tugasId)
{
    $pengumpulList = PengumpulanTugas::with('mahasiswa')
        ->where('tugas_id', $tugasId)
        ->get();

    Log::info("=== DEBUG PENGUMPULAN TUGAS ===");
    Log::info("Total pengumpulan: " . $pengumpulList->count());
    
    foreach ($pengumpulList as $pengumpulan) {
        Log::info("ID: {$pengumpulan->id}, Mahasiswa: {$pengumpulan->mahasiswa->nama}, File: '{$pengumpulan->file}', Nilai: {$pengumpulan->nilai}");
        
        if (empty($pengumpulan->file)) {
            Log::warning("WARNING: File kosong untuk mahasiswa {$pengumpulan->mahasiswa->nama}");
        }
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Debug info written to log',
        'data' => $pengumpulList->map(function($p) {
            return [
                'id' => $p->id,
                'mahasiswa' => $p->mahasiswa->nama,
                'file' => $p->file,
                'nilai' => $p->nilai,
                'created_at' => $p->created_at
            ];
        })
    ]);
}

/**
 * TAMBAHAN: Method untuk cek struktur folder storage
 */
public function debugStorage()
{
    $basePath = storage_path('app/public/');
    $folders = [];
    
    if (is_dir($basePath)) {
        $items = scandir($basePath);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $fullPath = $basePath . $item;
                if (is_dir($fullPath)) {
                    $folders[] = $item;
                    Log::info("Folder found: " . $item);
                }
            }
        }
    }
    
    return response()->json([
        'success' => true,
        'base_path' => $basePath,
        'folders' => $folders
    ]);
}

    /**
     * METODE BARU: Validasi file DOCX sebelum ekstraksi
     */
    private function isValidDocxFile($filePath)
    {
        try {
            // Cek ekstensi file
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($extension !== 'docx') {
                Log::error('File bukan DOCX: ' . $extension);
                return false;
            }

            // Cek apakah file bisa dibaca sebagai ZIP (DOCX adalah file ZIP)
            $zip = new \ZipArchive();
            $result = $zip->open($filePath, \ZipArchive::CHECKCONS);
            
            if ($result !== TRUE) {
                Log::error('File tidak dapat dibuka sebagai ZIP archive. Error code: ' . $result);
                return false;
            }

            // Cek apakah ada file penting dalam DOCX
            $requiredFiles = ['word/document.xml', '[Content_Types].xml'];
            foreach ($requiredFiles as $requiredFile) {
                if ($zip->locateName($requiredFile) === false) {
                    Log::error('File DOCX tidak memiliki struktur yang valid. Missing: ' . $requiredFile);
                    $zip->close();
                    return false;
                }
            }

            $zip->close();
            Log::info('File DOCX valid: ' . $filePath);
            return true;

        } catch (\Exception $e) {
            Log::error('Error validating DOCX file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Panggil Flask API untuk similarity scoring
     */
    private function callSimilarityAPI($text1, $text2, $method = 'cosine')
{
    try {
        Log::info('Memanggil Flask API untuk similarity scoring');

        $response = Http::timeout($this->apiTimeout)
            ->retry(3, 1000) // Retry 3 kali dengan delay 1 detik
            ->post($this->flaskApiUrl . '/similarity', [
                'text1' => $text1,
                'text2' => $text2,
                'method' => $method
            ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['score'])) {
                // PERBAIKAN: Kembalikan raw score tanpa konversi di sini
                // Biarkan convertScoreToFloat yang menangani konversi
                $rawScore = $data['score'];
                Log::info("Raw similarity score received: {$rawScore}");
                return $rawScore;
            } else {
                Log::error('Response tidak mengandung score: ' . $response->body());
                return null;
            }
        } else {
            Log::error('Flask API error: ' . $response->status() . ' - ' . $response->body());
            return null;
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('Connection error ke Flask API: ' . $e->getMessage());
        return null;
    } catch (\Exception $e) {
        Log::error('Error saat memanggil Flask API: ' . $e->getMessage());
        return null;
    }
}

    /**
     * Test koneksi ke Flask API
     */
    public function testFlaskConnection()
    {
        try {
            $response = Http::timeout(10)->get($this->flaskApiUrl . '/health');
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Flask API connected successfully',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Flask API connection failed',
                    'status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Flask API connection error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validasi file DOCX dengan PhpWord
     */
    private function validateDocxFile($file)
    {
        if (!$file) return false;

        try {
            Log::info('Validating DOCX file: ' . $file->getClientOriginalName());
            $tempPath = $file->getRealPath();
            $phpWord = IOFactory::load($tempPath);
            Log::info('DOCX file validation successful');
            return true;
        } catch (\Exception $e) {
            Log::error('DOCX validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract text dari file DOCX - IMPROVED VERSION
     */
    private function extractTextFromDocx($filePath)
    {
        Log::info("Mengekstrak teks dari file: " . $filePath);

        if (!file_exists($filePath)) {
            Log::warning("File tidak ditemukan: " . $filePath);
            return '';
        }

        // Validasi file sebelum ekstraksi
        if (!$this->isValidDocxFile($filePath)) {
            Log::error("File tidak valid atau corrupt: " . $filePath);
            return '';
        }

        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    } elseif (method_exists($element, 'getElements')) {
                        // Handle nested elements like tables, textboxes, etc.
                        $this->extractFromNestedElements($element, $text);
                    }
                }
            }

            $text = trim(preg_replace('/\s+/', ' ', $text)); // Clean up whitespace
            Log::info("Berhasil mengekstrak teks, panjang: " . strlen($text) . " karakter");
            
            if (strlen($text) < 10) {
                Log::warning("Teks yang diekstrak terlalu pendek, mungkin ada masalah dengan file");
            }
            
            return $text;

        } catch (\PhpOffice\PhpWord\Exception\IOException $e) {
            Log::error("Error saat membaca file DOCX (IOException): " . $e->getMessage());
            return '';
        } catch (\PhpOffice\PhpWord\Exception\InvalidImageException $e) {
            Log::error("Error invalid image dalam DOCX: " . $e->getMessage());
            return '';
        } catch (\Exception $e) {
            Log::error("Error saat mengekstrak teks dari DOCX: " . $e->getMessage());
            return '';
        }
    }

    /**
     * METODE BARU: Extract text dari nested elements
     */
    private function extractFromNestedElements($element, &$text)
    {
        try {
            if (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $nestedElement) {
                    if (method_exists($nestedElement, 'getText')) {
                        $text .= $nestedElement->getText() . ' ';
                    } elseif (method_exists($nestedElement, 'getElements')) {
                        $this->extractFromNestedElements($nestedElement, $text);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error extracting from nested elements: " . $e->getMessage());
        }
    }

    /**
     * Generate feedback berdasarkan score
     */
    private function generateFeedback($score)
{
    if ($score >= 96) {
        return 'Luar Biasa! Jawaban sangat luar biasa dan hampir sempurna!';
    }
    if ($score >= 90) {
        return 'Sangat Bagus! Jawaban sangat baik dan hampir mendekati sempurna.';
    }
    if ($score >= 86) {
        return 'Sangat Baik! Jawaban sangat baik, namun masih ada ruang untuk sedikit perbaikan.';
    }
    if ($score >= 81) {
        return 'Bagus! Jawaban cukup baik, tetapi ada beberapa bagian yang perlu penyempurnaan.';
    }
    if ($score >= 76) {
        return 'Memuaskan! Jawaban sudah jelas, tetapi ada beberapa aspek yang perlu diperbaiki lebih lanjut.';
    }
    if ($score >= 71) {
        return 'Cukup! Jawaban sudah cukup memadai, meskipun masih terdapat beberapa kekurangan.';
    }
    if ($score >= 66) {
        return 'Cukup Layak! Jawaban sudah cukup, namun masih perlu banyak pengembangan.';
    }
    if ($score >= 61) {
        return 'Diterima! Jawaban mencakup sebagian besar informasi, namun masih banyak yang perlu ditingkatkan.';
    }
    if ($score >= 56) {
        return 'Perlu Peningkatan! Jawaban perlu lebih mendalam dan beberapa bagian perlu pengembangan lebih lanjut.';
    }
    if ($score >= 51) {
        return 'Cukup! Jawaban sudah cukup, namun masih memerlukan banyak perbaikan.';
    }
    if ($score >= 46) {
        return 'Di Bawah Harapan! Jawaban kurang memadai dan perlu perhatian lebih.';
    }
    if ($score >= 41) {
        return 'Kurang! Jawaban kurang sesuai, perlu lebih banyak belajar dan berusaha.';
    }
    if ($score >= 36) {
        return 'Sangat Kurang! Jawaban jauh dari harapan, perlu lebih banyak pemahaman terhadap materi.';
    }
    if ($score >= 31) {
        return 'Sangat Buruk! Jawaban hampir tidak sesuai dengan yang diharapkan, perlu banyak perbaikan.';
    }
    return 'Tidak Ada Jawaban atau Tidak Diterima! Jawaban tidak sesuai, perbaikan sangat diperlukan.';
}


    /**
     * Get statistics untuk tugas
     */
    public function getStatistik($kelasId, $tugasId)
    {
        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->whereNotNull('nilai')
            ->get();

        if ($pengumpulan->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada tugas yang dinilai'
            ]);
        }

        $scores = $pengumpulan->pluck('nilai');

        // Pembulatan dua angka di belakang koma
        $rataRata = round($scores->average(), 2);
        $nilaiTertinggi = round($scores->max(), 2);
        $nilaiTerendah = round($scores->min(), 2);

        return response()->json([
            'total_dinilai' => $pengumpulan->count(),
            'rata_rata' => $rataRata,
            'nilai_tertinggi' => $nilaiTertinggi,
            'nilai_terendah' => $nilaiTerendah,
            'distribusi' => [
                'excellent' => $pengumpulan->where('nilai', '>=', 9600)->count(),
                'great' => $pengumpulan->where('nilai', '>=', 9100)->where('nilai', '<', 9600)->count(),
                'very_good' => $pengumpulan->where('nilai', '>=', 8600)->where('nilai', '<', 9100)->count(),
                'good' => $pengumpulan->where('nilai', '>=', 8100)->where('nilai', '<', 8600)->count(),
                'satisfactory' => $pengumpulan->where('nilai', '>=', 7600)->where('nilai', '<', 8100)->count(),
                'fair' => $pengumpulan->where('nilai', '>=', 7100)->where('nilai', '<', 7600)->count(),
                'adequate' => $pengumpulan->where('nilai', '>=', 6600)->where('nilai', '<', 7100)->count(),
                'needs_improvement' => $pengumpulan->where('nilai', '>=', 6100)->where('nilai', '<', 6600)->count(),
                'acceptable' => $pengumpulan->where('nilai', '>=', 5600)->where('nilai', '<', 6100)->count(),
                'poor' => $pengumpulan->where('nilai', '>=', 3100)->where('nilai', '<', 5100)->count(),
                'very_poor' => $pengumpulan->where('nilai', '>=', 3100)->where('nilai', '<', 3600)->count(),
                'extremely_poor' => $pengumpulan->where('nilai', '<', 3100)->count(),
            ]
        ]);
    }

/**
 * Menampilkan halaman utama rekap nilai (daftar kelas)
 */
public function rekapNilai(Request $request)
{
    try {
        // Ambil semua kelas yang diajar oleh dosen yang sedang login
        $kelasList = Kelas::where('dosen_id', Auth::id())
            ->with(['mahasiswa', 'tugas' => function($query) {
                $query->whereNotNull('id'); // Hanya kelas yang memiliki tugas
            }])
            ->get();

        // Filter hanya kelas yang memiliki tugas
        $kelasList = $kelasList->filter(function($kelas) {
            return $kelas->tugas->count() > 0;
        });

        // Ambil kelas yang dipilih dari request
        $selectedKelasId = $request->get('kelas_id');
        
        // Inisialisasi variabel tugas
        $tugas = collect(); // Collection kosong sebagai default
        
        // Jika ada kelas yang dipilih, ambil data tugas dengan pengumpulan
        if ($selectedKelasId) {
            // Validasi bahwa kelas yang dipilih adalah milik dosen yang login
            $selectedKelas = $kelasList->firstWhere('id', $selectedKelasId);
            
            if ($selectedKelas) {
                $tugas = Tugas::where('kelas_id', $selectedKelasId)
                    ->with([
                        'kelas',
                        'pengumpulanTugas' => function($query) {
                            $query->whereNotNull('nilai') // Hanya yang sudah dinilai
                                  ->with('mahasiswa');
                        }
                    ])
                    ->get();
            }
        }

        return view('dosen.rekap_nilai.index', compact(
            'kelasList',
            'selectedKelasId', 
            'tugas'
        ));

    } catch (\Exception $e) {
        Log::error('Error dalam rekapNilai index: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data rekap nilai: ' . $e->getMessage());
    }
}

/**
 * Menampilkan rekap nilai per kelas (untuk backward compatibility)
 * Redirect ke index dengan parameter kelas_id
 */
public function rekapPerKelas($kelasId)
{
    return redirect()->route('dosen.rekap_nilai.index', ['kelas_id' => $kelasId]);
}

/**
 * Export rekap nilai ke Excel/CSV
 */
public function exportRekap($kelasId)
{
    try {
        // Validasi akses dosen
        $kelas = Kelas::where('dosen_id', Auth::id())->findOrFail($kelasId);
        
        // Ambil semua tugas dalam kelas beserta pengumpulan yang sudah dinilai
        $tugas = Tugas::where('kelas_id', $kelasId)
            ->with([
                'pengumpulanTugas' => function($query) {
                    $query->whereNotNull('nilai')
                          ->with('mahasiswa')
                          ->orderBy('nilai', 'desc');
                }
            ])
            ->get();

        // Jika tidak ada data tugas atau pengumpulan
        if ($tugas->isEmpty() || $tugas->every(function($t) { return $t->pengumpulanTugas->isEmpty(); })) {
            return redirect()->back()->with('error', 'Tidak ada data nilai untuk diekspor.');
        }

        return $this->exportRekapKelas($kelas, $tugas);

    } catch (\Exception $e) {
        Log::error('Error export rekap: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
    }
}

/**
 * Export rekap nilai untuk semua tugas dalam kelas
 */
private function exportRekapKelas($kelas, $tugasList = null)
{
    try {
        // Jika tugasList tidak diberikan, ambil dari database
        if (!$tugasList) {
            $tugasList = Tugas::where('kelas_id', $kelas->id)
                ->with([
                    'pengumpulanTugas' => function($query) {
                        $query->whereNotNull('nilai')
                              ->with('mahasiswa')
                              ->orderBy('nilai', 'desc');
                    }
                ])
                ->get();
        }

        $filename = 'Rekap_Nilai_Kelas_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($kelas, $tugasList) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header informasi
            fputcsv($file, ['REKAP NILAI KELAS']);
            fputcsv($file, ['Kelas', $kelas->nama_kelas]);
            fputcsv($file, ['Mata Kuliah', $kelas->nama_matakuliah ?? '-']);
            fputcsv($file, ['Dosen', Auth::user()->name]);
            fputcsv($file, ['Tanggal Export', date('d/m/Y H:i:s')]);
            fputcsv($file, []);

            foreach ($tugasList as $tugas) {
                // Skip jika tidak ada pengumpulan yang dinilai
                if ($tugas->pengumpulanTugas->isEmpty()) {
                    continue;
                }

                fputcsv($file, ['TUGAS: ' . $tugas->judul]);
                fputcsv($file, ['Tipe: ' . ucfirst($tugas->tipe)]);
                fputcsv($file, ['Deadline: ' . \Carbon\Carbon::parse($tugas->deadline)->format('d M Y H:i')]);
                fputcsv($file, []);
                
                // Header kolom
                fputcsv($file, [
                    'No',
                    'NIM', 
                    'Nama Mahasiswa', 
                    'Nilai', 
                    'Grade',
                    'Feedback',
                    'Tanggal Dinilai',
                    'Tanggal Pengumpulan'
                ]);
                
                // Data pengumpulan untuk tugas ini
                $no = 1;
                foreach ($tugas->pengumpulanTugas as $pengumpulan) {
                    fputcsv($file, [
                        $no++,
                        $pengumpulan->mahasiswa->nim ?? '-',
                        $pengumpulan->mahasiswa->name ?? $pengumpulan->mahasiswa->nama ?? '-',
                        $pengumpulan->nilai,
                        $this->getNilaiGrade($pengumpulan->nilai),
                        $pengumpulan->feedback ?? '-',
                        $pengumpulan->dinilai_pada ? Carbon::parse($pengumpulan->dinilai_pada)->format('d/m/Y H:i') : '-',
                        Carbon::parse($pengumpulan->created_at)->format('d/m/Y H:i')
                    ]);
                }
                
                fputcsv($file, []); // Baris kosong antar tugas
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    } catch (\Exception $e) {
        Log::error('Error export rekap kelas: ' . $e->getMessage());
        throw $e;
    }
}


/**
 * Helper method untuk menghitung distribusi nilai
 */
private function hitungDistribusiNilai($pengumpulanList, $totalDinilai)
{
    $distribusi = [
        'A' => ['label' => 'Luar Biasa (≥96)', 'count' => 0, 'percentage' => 0, 'color' => 'success'],
        'A-' => ['label' => 'Sangat Bagus (90-95)', 'count' => 0, 'percentage' => 0, 'color' => 'success'],
        'B+' => ['label' => 'Sangat Baik (86-89)', 'count' => 0, 'percentage' => 0, 'color' => 'info'],
        'B' => ['label' => 'Bagus (81-85)', 'count' => 0, 'percentage' => 0, 'color' => 'info'],
        'B-' => ['label' => 'Memuaskan (76-80)', 'count' => 0, 'percentage' => 0, 'color' => 'primary'],
        'C+' => ['label' => 'Cukup (71-75)', 'count' => 0, 'percentage' => 0, 'color' => 'warning'],
        'C' => ['label' => 'Cukup Layak (66-70)', 'count' => 0, 'percentage' => 0, 'color' => 'warning'],
        'C-' => ['label' => 'Diterima (61-65)', 'count' => 0, 'percentage' => 0, 'color' => 'warning'],
        'D+' => ['label' => 'Perlu Peningkatan (56-60)', 'count' => 0, 'percentage' => 0, 'color' => 'danger'],
        'D' => ['label' => 'Cukup (51-55)', 'count' => 0, 'percentage' => 0, 'color' => 'danger'],
        'E' => ['label' => 'Kurang (<51)', 'count' => 0, 'percentage' => 0, 'color' => 'dark']
    ];

    foreach ($pengumpulanList as $pengumpulan) {
        $nilai = $pengumpulan->nilai;
        
        if ($nilai >= 96) $distribusi['A']['count']++;
        elseif ($nilai >= 90) $distribusi['A-']['count']++;
        elseif ($nilai >= 86) $distribusi['B+']['count']++;
        elseif ($nilai >= 81) $distribusi['B']['count']++;
        elseif ($nilai >= 76) $distribusi['B-']['count']++;
        elseif ($nilai >= 71) $distribusi['C+']['count']++;
        elseif ($nilai >= 66) $distribusi['C']['count']++;
        elseif ($nilai >= 61) $distribusi['C-']['count']++;
        elseif ($nilai >= 56) $distribusi['D+']['count']++;
        elseif ($nilai >= 51) $distribusi['D']['count']++;
        else $distribusi['E']['count']++;
    }

    // Hitung persentase untuk setiap kategori
    foreach ($distribusi as $key => &$kategori) {
        $kategori['percentage'] = $totalDinilai > 0 ? round(($kategori['count'] / $totalDinilai) * 100, 1) : 0;
    }

    return $distribusi;
}

/**
 * Helper method untuk menentukan grade nilai
 */
private function getNilaiGrade($nilai)
{
    if ($nilai >= 96) return 'A';
    if ($nilai >= 90) return 'A-';
    if ($nilai >= 86) return 'B+';
    if ($nilai >= 81) return 'B';
    if ($nilai >= 76) return 'B-';
    if ($nilai >= 71) return 'C+';
    if ($nilai >= 66) return 'C';
    if ($nilai >= 61) return 'C-';
    if ($nilai >= 56) return 'D+';
    if ($nilai >= 51) return 'D';
    return 'E';
}

/**
 * Export rekap nilai ke Excel/CSV (method dari kode asli)
 */
private function exportRekapNilai($data, $tugas, $kelas)
{
    try {
        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $tugas->judul) . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $tugas, $kelas) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header informasi
            fputcsv($file, ['REKAP NILAI TUGAS']);
            fputcsv($file, ['Kelas', $kelas->nama_kelas]);
            fputcsv($file, ['Tugas', $tugas->judul]);
            fputcsv($file, ['Tanggal Export', date('d/m/Y H:i:s')]);
            fputcsv($file, []);
            
            // Header kolom
            fputcsv($file, [
                'No',
                'NIM', 
                'Nama Mahasiswa', 
                'Nilai', 
                'Grade',
                'Feedback',
                'Tanggal Dinilai',
                'Tanggal Pengumpulan'
            ]);
            
            // Data
            $no = 1;
            foreach ($data as $row) {
                fputcsv($file, [
                    $no++,
                    $row['nim'],
                    $row['nama'],
                    $row['nilai'],
                    $row['grade'],
                    $row['feedback'],
                    $row['tanggal_dinilai'],
                    $row['tanggal_pengumpulan']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    } catch (\Exception $e) {
        Log::error('Error export rekap nilai: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Refresh rekap nilai (untuk reload data terbaru) - AJAX endpoint
 */
public function refreshRekapNilai($kelasId, $tugasId = null)
{
    try {
        if ($tugasId) {
            // Refresh untuk tugas tertentu
            $pengumpulanList = PengumpulanTugas::with('mahasiswa')
                ->where('tugas_id', $tugasId)
                ->whereNotNull('nilai')
                ->orderBy('nilai', 'desc')
                ->get();

            $totalDinilai = $pengumpulanList->count();
            $totalPengumpul = PengumpulanTugas::where('tugas_id', $tugasId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Data rekap berhasil diperbarui',
                'data' => [
                    'total_dinilai' => $totalDinilai,
                    'total_pengumpul' => $totalPengumpul,
                    'last_updated' => now()->format('d/m/Y H:i:s')
                ]
            ]);
        } else {
            // Refresh untuk semua tugas dalam kelas
            $tugasList = Tugas::where('kelas_id', $kelasId)->get();
            $totalTugas = $tugasList->count();
            $totalDinilai = 0;
            $totalPengumpul = 0;

            foreach ($tugasList as $tugas) {
                $totalDinilai += PengumpulanTugas::where('tugas_id', $tugas->id)
                    ->whereNotNull('nilai')
                    ->count();
                $totalPengumpul += PengumpulanTugas::where('tugas_id', $tugas->id)->count();
            }

            return response()->json([
                'success' => true,
                'message' => 'Data rekap kelas berhasil diperbarui',
                'data' => [
                    'total_tugas' => $totalTugas,
                    'total_dinilai' => $totalDinilai,
                    'total_pengumpul' => $totalPengumpul,
                    'last_updated' => now()->format('d/m/Y H:i:s')
                ]
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error refresh rekap nilai: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui data: ' . $e->getMessage()
        ], 500);
    }
}}