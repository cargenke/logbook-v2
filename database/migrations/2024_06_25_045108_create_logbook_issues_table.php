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
        Schema::create('logbook_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('logbook_id')->nullable();
            $table->string('chasisNumber')->nullable();
            $table->string('regNumber')->nullable();
            $table->longText('remarks')->nullable();
            $table->dateTime('createdOn')->nullable();
            $table->integer('createdBy')->nullable()->comment('staff creating logbook issues');
            $table->dateTime('updatedOn')->nullable();
            $table->integer('updatedBy')->nullable();
            $table->integer('requesterId')->nullable()->comment('staff requesting transfer');
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
        Schema::dropIfExists('logbook_issues');
    }
};
