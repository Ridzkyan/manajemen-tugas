<?php

namespace App\Exports;

use App\Models\Tugas\Tugas;use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class RekapNilaiExport implements FromCollection, WithHeadings
{
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        return Tugas::where('kelas_id', $this->kelasId)
            ->with('kelas')
            ->get()
            ->map(function ($tugas) {
                return [
                    $tugas->kelas->nama_kelas ?? '-',
                    $tugas->judul,
                    $tugas->nilai ?? 'Belum dinilai',
                    $tugas->feedback ?? '-',
                    $tugas->deadline ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return ['Kelas', 'Judul Tugas', 'Nilai', 'Feedback', 'Deadline'];
    }
}
