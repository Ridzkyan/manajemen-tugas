<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Kelas\Materi;

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

    // Kelas dimiliki oleh 1 Dosen
    public function dosen()
    {
    return $this->belongsTo(\App\Models\User::class, 'dosen_id');
    }

        public function materis()
    {
        return $this->hasMany(Materi::class, 'kelas_id');
    }

    // Kelas punya banyak Mahasiswa
    public function mahasiswas()
    {
        return $this->belongsToMany(User::class, 'kelas_mahasiswa', 'kelas_id', 'mahasiswa_id');
    }
    
}
