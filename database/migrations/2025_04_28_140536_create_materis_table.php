<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterisTable extends Migration
{
    public function up()
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('judul');
            $table->enum('tipe', ['pdf', 'link']);
            $table->string('file')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        });
    }

    public function down()
    {
        Schema::dropIfExists('materis');
    }
}
