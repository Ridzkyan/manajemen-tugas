<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkWaToKelasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('kelas', function (Blueprint $table) {
        $table->string('link_wa')->nullable()->after('nama_matakuliah');
    });
}

    public function down()
{
    Schema::table('kelas', function (Blueprint $table) {
        $table->dropColumn('link_wa');
    });
}

}
