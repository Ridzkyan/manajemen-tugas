<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengumpulanTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('pengumpulan_tugas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
        $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
        $table->string('file')->nullable();
        $table->float('nilai')->nullable();
        $table->text('feedback')->nullable();
        $table->timestamps();
    });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
}
