<?php

namespace App\Models\Kelas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Tugas\Tugas;
use App\Models\Kelas\Materi;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas', 'nama_matakuliah', 'kode_unik', 'dosen_id', 'whatsapp_link',
    ];

    public function materis()
    {
        return $this->hasMany(Materi::class, 'kelas_id');
    }

    public function dosen()
    {
    return $this->belongsTo(\App\Models\User::class, 'dosen_id');
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
