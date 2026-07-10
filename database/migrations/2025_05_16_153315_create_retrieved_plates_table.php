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
        Schema::create('retrieved_plates', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number');
            $table->string('chassis_number')->nullable();
            $table->string('current_status');
            $table->json('status_history');
            $table->timestamps();
            $table->softDeletes();

            $table->index('registration_number');
            $table->index('chassis_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retrieved_plates');
    }
};
