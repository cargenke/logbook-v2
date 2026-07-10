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
        Schema::create('system_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('ObjType')->nullable();
            $table->string('name')->nullable();
            $table->string('isActive')->default(1)->comment('0=>No,1=>Yes')->nullable();
            $table->dateTime('createdOn')->nullable();
            $table->string('createdBy')->nullable();
            $table->dateTime('editedOn')->nullable();
            $table->string('editedBy')->nullable();
            $table->dateTime('activatedOn')->nullable();
            $table->string('activatedBy')->nullable();
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
        Schema::dropIfExists('system_statuses');
    }
};
