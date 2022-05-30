<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyJustificationState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('justifications', function (Blueprint $table) {
            $table->enum('state', ['processing', 'justified', 'acknowledged', 'rejected'])->default('processing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('justifications', function (Blueprint $table) {
            $table->enum('state', ['processing', 'acknowledged', 'rejected'])->default('processing');
        });
    }
}
