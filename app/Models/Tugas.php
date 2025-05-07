<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id', 
        'judul', 
        'tipe', 
        'deskripsi', 
        'file_soal', 
        'deadline', 
        'nilai', // penambahan nilai
        'feedback', // feeback
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mahasiswa()
    {
        return $this->hasManyThrough(KelasMahasiswa::class, Kelas::class);
    }
}
