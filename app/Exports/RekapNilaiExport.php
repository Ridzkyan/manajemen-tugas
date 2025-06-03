<?php

namespace App\Exports;

use App\Models\Tugas\Tugas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapNilaiExport implements FromCollection, WithHeadings
{
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function collection(): Collection
    {
        $tugasList = Tugas::where('kelas_id', $this->kelasId)
            ->with(['kelas', 'pengumpulanTugas.mahasiswa'])
            ->get();

        $data = collect();

        foreach ($tugasList as $tugas) {
            foreach ($tugas->pengumpulanTugas as $pengumpulan) {
                $data->push([
                    'Nama Mahasiswa' => $pengumpulan->mahasiswa->name ?? '-',
                    'Kelas'          => $tugas->kelas->nama_kelas ?? '-',
                    'Mata Kuliah'    => $tugas->kelas->nama_matakuliah ?? '-',
                    'Judul Tugas'    => $tugas->judul,
                    'Nilai'          => $pengumpulan->nilai ?? 'Belum Dinilai',
                    'Feedback'       => $pengumpulan->feedback ?? '-',
                    'Deadline'       => $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('d-m-Y H:i') : '-',
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'Kelas',
            'Mata Kuliah',
            'Judul Tugas',
            'Nilai',
            'Feedback',
            'Deadline',
        ];
    }
}
