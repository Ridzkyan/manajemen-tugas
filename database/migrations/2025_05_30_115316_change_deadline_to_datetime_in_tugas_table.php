<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDeadlineToDatetimeInTugasTable extends Migration
{
    public function up()
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dateTime('deadline')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->date('deadline')->nullable()->change();
        });
    }
}

