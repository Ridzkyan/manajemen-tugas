<?php

namespace App\Models\Tugas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas\Kelas;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id', 'judul', 'tipe', 'deskripsi', 'file_soal', 'deadline', 'nilai' => 'float', 'feedback', 'mahasiswa_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(\App\Models\User\Mahasiswa::class, 'mahasiswa_id');
    }
}
