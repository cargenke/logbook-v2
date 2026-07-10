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
        Schema::create('notification_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('requester_id')->nullable()->comment('id from requests');
            $table->integer('notification_id')->nullable()->comment('id from notification');
            $table->string('status')->nullable()->comment('Success,Error, from VAS PRO');
            $table->longText('statusDescription')->nullable()->comment('from VAS PRO');
            $table->longText('message')->nullable()->comment('message from VAS PRO');
            $table->dateTime('createdOn')->nullable();
            $table->integer('createdBy')->nullable()->comment('staff sending notification');
            $table->dateTime('updatedOn')->nullable();
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
        Schema::dropIfExists('notification_lines');
    }
};
