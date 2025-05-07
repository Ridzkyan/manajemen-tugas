<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamaMatakuliahToKelasTable extends Migration
{
    /**
     * Menjalankan migrasi untuk menambah kolom.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Cek apakah kolom 'nama_matakuliah' sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('kelas', 'nama_matakuliah')) {
                $table->string('nama_matakuliah', 255);
            }
        });
    }

    /**
     * Membatalkan migrasi (rollback).
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('nama_matakuliah');
        });
    }
}