<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'nama_matakuliah',
        'kode_unik',
        'dosen_id',
        'whatsapp_link',
    ];

    public function materi()
    {
        return $this->hasMany(\App\Models\Materi::class);
    }
    
    // Kelas dimiliki oleh 1 Dosen
    public function dosen()
    {
    return $this->belongsTo(\App\Models\User::class, 'dosen_id');
    }

    // Kelas punya banyak Mahasiswa
    public function mahasiswas()
    {
        return $this->belongsToMany(User::class, 'kelas_mahasiswa', 'kelas_id', 'mahasiswa_id');
    }
    
}
