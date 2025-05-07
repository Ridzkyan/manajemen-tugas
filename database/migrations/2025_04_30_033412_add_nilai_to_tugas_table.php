<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiToTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('tugas', function (Blueprint $table) {
        $table->integer('nilai')->nullable();  // Kolom untuk nilai
        $table->text('feedback')->nullable();  // Kolom untuk feedback
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('tugas', function (Blueprint $table) {
        $table->dropColumn(['nilai', 'feedback']);
    });
    }
}
