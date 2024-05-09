<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperativeSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperative_shares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('social_walfare_id');
            $table->integer('bracket')->nullable();
            $table->integer('to_bracket')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('status')->default(0);
            $table->softDeletes();
            $table->foreign('social_walfare_id')->references('id')->on('social_welfares')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('cooperative_shares');
    }
}
