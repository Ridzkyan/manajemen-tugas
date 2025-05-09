<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTugasTable extends Migration
{
    public function up()
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('judul');
            $table->enum('tipe', ['tugas', 'ujian']);
            $table->text('deskripsi')->nullable();
            $table->string('file_soal')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tugas');
    }
}
