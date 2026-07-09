<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_request_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('request_id')->nullable();
            $table->string('chasisNumber', 50)->nullable();
            $table->string('regNumber', 50)->nullable();
            $table->string('name1', 50)->nullable();
            $table->string('name2', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('modeofpayment', 50)->nullable();
            $table->string('tel1', 50)->nullable();
            $table->string('tel2', 50)->nullable();
            $table->string('IDNo', 50)->nullable();
            $table->string('PinNo1', 50)->nullable();
            $table->string('PinNo2', 50)->nullable();
            $table->string('PinNo3', 50)->nullable();
            $table->timestamp('createdOn')->nullable();
            $table->integer('createdBy')->nullable();
            $table->timestamp('updatedOn')->nullable();
            $table->integer('updatedBy')->nullable();
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
        Schema::dropIfExists('transfer_request_logs');
    }
};
