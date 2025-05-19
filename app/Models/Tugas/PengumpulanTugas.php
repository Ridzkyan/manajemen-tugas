<?php

namespace App\Models\Tugas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tugas\Tugas;use App\Models\User\Mahasiswa;;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $fillable = ['tugas_id', 'mahasiswa_id', 'file', 'nilai', 'feedback'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
