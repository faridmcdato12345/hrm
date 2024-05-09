<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusMonthDeductionToCashAdvances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->string('month_deduction')->nullable();
            $table->integer('status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropColumn('month_deduction');
            $table->dropColumn('status');
        });
    }
}
