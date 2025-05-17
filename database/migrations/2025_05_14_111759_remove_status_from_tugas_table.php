<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStatusFromTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('tugas', function (Blueprint $table) {
        if (Schema::hasColumn('tugas', 'status')) {
            $table->dropColumn('status');
        }
    });
}


public function down()
{
    Schema::table('tugas', function (Blueprint $table) {
        $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
    });
}

}
