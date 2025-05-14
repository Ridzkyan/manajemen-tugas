<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = ['kelas_id', 'judul', 'tipe', 'file', 'link'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
