<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('logbook_requests', function (Blueprint $table) {
            $table->integer('is_instant_transfer')->default(0)->comment('0=>No,1=>Yes');
            $table->integer('assign_to')->nullable()->comment('0=>No,1=>Yes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook_requests', function (Blueprint $table) {
            //
        });
    }
};
