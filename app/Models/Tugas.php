<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id', 'judul', 'tipe', 'deskripsi', 'file_soal', 'deadline', 'nilai', 'feedback',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}

