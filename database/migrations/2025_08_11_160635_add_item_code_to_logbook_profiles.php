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
        Schema::table('logbook_profiles', function (Blueprint $table) {
            $table->string('ItemCode', 50)->after('data_source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logbook_profiles', function (Blueprint $table) {
            $table->string('ItemCode')->nullable();
        });
    }
};
