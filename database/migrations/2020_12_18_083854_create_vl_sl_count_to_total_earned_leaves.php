<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVlSlCountToTotalEarnedLeaves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_earned_leaves', function (Blueprint $table) {
            $table->integer('vl_count')->nullable();
            $table->integer('sl_count')->nullable();
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
            $table->dropColumn('vl_count');
            $table->dropColumn('sl_count');
        });
    }
}
