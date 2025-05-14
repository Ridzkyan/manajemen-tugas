<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tugas;
use App\Models\Materi;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas', 'nama_matakuliah', 'kode_unik', 'dosen_id', 'whatsapp_link',
    ];

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(User::class, 'kelas_mahasiswa', 'kelas_id', 'mahasiswa_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }
}
