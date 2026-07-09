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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('requester_id')->nullable()->comment('staff requesting transfer');
            $table->string('status_id')->nullable()->comment('from the system status');
            $table->string('status')->nullable()->comment('success,failed');
            $table->string('notificationType')->nullable();
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
        Schema::dropIfExists('notifications');
    }
};
