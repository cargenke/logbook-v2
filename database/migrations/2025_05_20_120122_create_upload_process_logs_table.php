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
        Schema::create('upload_process_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable()->comment('Process Name');
            $table->string('file_name')->nullable();
            $table->string('status')->nullable()->comment('0 => No Process Running, 1 => Process Running');
            $table->timestamp('createdOn')->nullable()->comment();
            $table->integer('createdBy')->nullable()->comment();
            $table->timestamp('editedOn')->nullable()->comment();
            $table->integer('editedBy')->nullable()->comment();
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
        Schema::dropIfExists('upload_process_logs');
    }
};
