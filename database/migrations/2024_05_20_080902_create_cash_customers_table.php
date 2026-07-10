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
        Schema::create('cash_customers', function (Blueprint $table) {
            $table->id();
            $table->string('CardCode')->nullable()->comment('customer code');
            $table->string('CardName')->nullable()->comment('customer name');
            $table->string('Location')->nullable()->comment('location');
            $table->string('Active')->nullable()->comment('Y=>Yes, N=>No,Valid For');
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
        Schema::dropIfExists('cash_customers');
    }
};
