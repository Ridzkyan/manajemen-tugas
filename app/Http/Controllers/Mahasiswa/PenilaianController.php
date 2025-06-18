<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tugas\Tugas;
use App\Models\User\Mahasiswa;
use App\Notifications\TugasDinilaiNotification;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Tampilkan halaman penilaian untuk tugas tertentu.
     */
    public function show($kelasId, $tugasId)
    {
        $tugas = Tugas::where('id', $tugasId)
                      ->where('kelas_id', $kelasId)
                      ->firstOrFail();

        return view('mahasiswa.kelas.tugas.penilaian', compact('tugas'));
    }

    /**
     * Simpan nilai dan feedback, lalu kirim notifikasi ke mahasiswa.
     */
    public function update(Request $request, $kelasId, $tugasId)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $tugas = Tugas::where('id', $tugasId)
                    ->where('kelas_id', $kelasId)
                    ->firstOrFail();

        // Membatasi nilai menjadi 2 angka di belakang koma
        // Membatasi nilai menjadi 2 angka di belakang koma
        $tugas->nilai = number_format($tugas->nilai / 100, 2, ',', '.');  // Memformat nilai menjadi 2 angka di belakang koma

        $tugas->feedback = $request->feedback;
        $tugas->save();

        if ($tugas->mahasiswa) {
            $tugas->mahasiswa->notify(new TugasDinilaiNotification($tugas));
        }

        return redirect()->route('mahasiswa.kelas.tugas.index', [$kelasId])
                        ->with('success', 'Penilaian berhasil disimpan dan notifikasi telah dikirim.');
    }
}
