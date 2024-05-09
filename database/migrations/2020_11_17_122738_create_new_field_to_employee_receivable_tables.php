<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewFieldToEmployeeReceivableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_receivables', function (Blueprint $table) {
            $table->string('property_number')->nullable();
            $table->string('serial_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_receivables', function (Blueprint $table) {
            $table->dropColumn('property_number');
            $table->dropColumn('serial_number');
        });
    }
}
