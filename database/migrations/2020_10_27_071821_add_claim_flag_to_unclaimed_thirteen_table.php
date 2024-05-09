<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClaimFlagToUnclaimedThirteenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unclaimed_thirteens', function (Blueprint $table) {
            $table->integer('flag')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unclaimed_thirteens', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
}
