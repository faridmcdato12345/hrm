<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('role_id')->nullable();
            $table->string('username')->unique();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('password')->nullable();
            $table->string('current_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('designation')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->string('employment_status')->nullable();
            $table->string('picture')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->integer('basic_salary')->default(0);
            $table->integer('bonus')->default(0)->nullable();
            $table->integer('total_salary')->default(0)->nullable();
            $table->rememberToken();
            $table->softDeletes()->nullable();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('employees');
    }
}
