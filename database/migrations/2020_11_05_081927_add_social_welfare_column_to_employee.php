<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialWelfareColumnToEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('sss')->nullable();
            $table->string('pag_ibig')->nullable();
            $table->string('philhealth')->nullable();
            $table->string('tin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('sss');
            $table->dropColumn('pag_ibig');
            $table->dropColumn('philhealth');
            $table->dropColumn('tin');
        });
    }
}
