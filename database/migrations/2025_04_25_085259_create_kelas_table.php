<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelasTable extends Migration
{
    public function up()
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('nama_matakuliah');
            $table->string('kode_unik')->unique(); // Kode unik matakuliah
            $table->unsignedBigInteger('dosen_id'); // ID dosen pembuat
            $table->timestamps();

            // Foreign key ke tabel users (dosen)
            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelas');
    }
}
