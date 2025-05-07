<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeUnikToKelasTable extends Migration
{
    public function up()
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'kode_unik')) {
                $table->string('kode_unik')->nullable()->after('nama_matakuliah');
            }
        });
    }

    public function down()
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('kode_unik');
        });
    }
}
