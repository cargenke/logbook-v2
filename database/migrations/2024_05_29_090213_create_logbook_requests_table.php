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
        Schema::create('logbook_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('logbook_id')->nullable();
            $table->string('chasisNumber')->nullable();
            $table->string('regNumber')->nullable();
            $table->string('name1')->nullable();
            $table->string('name2')->nullable();
            $table->string('email')->nullable();
            $table->string('modeofpayment')->nullable();
            $table->string('tel1')->nullable();
            $table->string('tel2')->nullable();
            $table->string('IDNo')->nullable();
            $table->string('PinNo1')->nullable();
            $table->string('PinNo2')->nullable();
            $table->string('PinNo3')->nullable();
            $table->dateTime('createdOn')->nullable();
            $table->string('createdBy')->nullable();
            $table->dateTime('updatedOn')->nullable();
            $table->string('updatedBy')->nullable();
            $table->string('ntsaApplicationNumber')->nullable();
            $table->integer('isClosed')->default(1)->comment('0=>No,1=>Yes, For Editing');
            $table->integer('isActive')->default(1)->comment('0=>No,1=>Yes');
            $table->string('status')->nullable()->comment('0=>No,1=>Yes');
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
        Schema::dropIfExists('logbook_requests');
    }
};
