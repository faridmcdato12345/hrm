<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddVlSlColumnOnTotalEarnedLeaves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_earned_leaves', function (Blueprint $table) {
            $table->integer('vl')->default(0);
            $table->integer('sl')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('total_earned_leaves', function (Blueprint $table) {
            $table->dropColumn('vl');
            $table->dropColumn('sl');
        });
    }
}
